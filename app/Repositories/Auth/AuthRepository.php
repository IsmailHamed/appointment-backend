<?php


namespace App\Repositories\Auth;


use App\Enums\UserType;
use App\Interfaces\Auth\AuthInterface;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\FileManagement;
use App\Transformers\BookingTransformer;
use App\Transformers\ExpertTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tzsk\Otp\Facades\Otp;
use Illuminate\Support\Facades\Auth;


class AuthRepository implements AuthInterface
{
    use  FileManagement, ApiResponse;

    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function register()
    {
        $data = $this->uploadImageAndAppendImageNameToRequest(User::$ImagesDir);
        $user = $this->model::create($data);
        return $this->loginUser($user);
    }

    public function login()
    {
        $email = request('email');
        $password = request('password');
        if (!$token = Auth::attempt(['email' => $email, 'password' => $password])) {
            return $this->errorMessage('Unauthorized', Response::HTTP_UNAUTHORIZED);
        } else {
            return $this->respondWithToken($token);
        }
    }

    public function loginAsGuest()
    {
        $uuid = request('uuid');
        $data = [
            'first_name' => $uuid,
            'last_name' => $uuid,
            'email' => $uuid . 'localhost.com',
            'password' => 'secret',
            'user_type' => UserType::GUEST,
        ];
        $user = $this->model::create($data);
        return $this->loginUser($user);

    }

    public function logout()
    {
        Auth::logout();
        return $this->successMessage('Successfully logged out');
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function update()
    {
        $user = Auth::user();
        $oldImageName = $user->image_name;
        $data = $this->uploadImageAndAppendImageNameToRequest(User::$ImagesDir);
        $isEmptyOrNullOldImageName = $this->IsNullOrEmptyString($oldImageName);
        $isUpdated = $user->fill($data)->save();
        $isChanged = ($oldImageName != $user->image_name) ? true : false;
        if ($isUpdated && !$isEmptyOrNullOldImageName && $isChanged) {
            Storage::delete(User::$ImagesDir . ' / ' . $oldImageName);
        }

        return $this->successMessage("User's information updated successfully");
    }

    public function forgetPassword()
    {
        $email = request('email');
        $user = User::whereEmail($email)->first();
        return $this->sendOTP($user->email);
    }

    protected function sendOTP($email)
    {
        $key = $email . config('app.key');
        $code = Otp::generate($key);
        try {
            Mail::to($email)->send(new \App\Mail\OTP($code));
        } catch (\Exception $e) {
            return $this->errorMessage("There is a problem while sending your code", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->successMessage("Code is sent successfully", Response::HTTP_OK);
    }

    public function resetPassword()
    {
        $email = request('email');

        $user = User::whereEmail($email)->first();

        if ($this->validateOTP($user->email)) {
            $user->fill(request()->only('password'))->save();
            return $this->successMessage("Password is updated successfully", Response::HTTP_OK);
        }
        return $this->errorMessage("The code is invalid", Response::HTTP_FORBIDDEN);
    }

    protected function validateOTP($email)
    {
        $key = $email . config('app.key');
        return Otp::match(request()->get('code'), $key);
    }

    public function requestEmailValidation()
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
//            TODO Test This
            return $this->successMessage("Email is already verified");
        }
        return $this->sendOTP($user->email);
    }

    public function validateEmail()
    {
        $user = Auth::user();
        if ($this->validateOTP($user->email)) {
            $user->markEmailAsVerified();
            Cache::delete($user->email);
            return $this->successMessage("Email is verified");
        }

        return $this->errorMessage("The code is invalid", Response::HTTP_FORBIDDEN);
    }

    protected function respondWithToken($token)
    {
        $user = Auth::user();
        $userInformation = [];
        if (!is_null($user) && $user->isExpert()) {
            $userInformation = fractal($user->expert, new ExpertTransformer())->toArray();

        } else {
            $userInformation = fractal($user, new UserTransformer())->toArray();
        }
        $authData = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'me' => $userInformation['data']
        ];
        return $this->successResponse($authData, Response::HTTP_OK);
    }

    /**
     * @param $user
     */
    private function loginUser($user)
    {
        $token = Auth::login($user);
        return $this->respondWithToken($token);
    }

}
