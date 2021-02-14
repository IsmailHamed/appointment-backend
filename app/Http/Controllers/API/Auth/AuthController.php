<?php


namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginAsGuestRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RequestForgetPasswordRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Http\Requests\Auth\ValidateEmailRequest;
use App\Interfaces\Auth\AuthInterface;
use App\Transformers\BookingTransformer;
use App\Transformers\UserTransformer;

/**
 * @group Auth management
 *  APIs for login, register and all about auth
 */
class AuthController extends Controller
{

    protected $auth;

    public function __construct(AuthInterface $auth)
    {
        $this
            ->middleware('transform.input:' . UserTransformer::class);
        $this
            ->middleware('auth:api')
            ->only(['logout','update']);
        $this->auth = $auth;

    }

    /**
     * Login
     * This endpoint lets you to login with specific user
     * @unauthenticated
     * @responseFile storage/responses/auth/register.json
     * @param LoginRequest $request
     * @return mixed
     */
    public function login(LoginRequest $request)
    {
        return $this->auth->login();
    }

    /**
     * Login
     * This endpoint lets you to login with specific user
     * @unauthenticated
     * @responseFile storage/responses/auth/register.json
     * @param LoginRequest $request
     * @return mixed
     */
    public function loginAsGuest(LoginAsGuestRequest $request)
    {
        return $this->auth->loginAsGuest();
    }

    /**
     * Register
     * This endpoint lets you to add a new user
     * @unauthenticated
     * @responseFile storage/responses/auth/register.json
     * @param RegisterRequest $request
     * @return mixed
     */
    public function register(RegisterRequest $request)
    {
        return $this->auth->register();
    }

    /**
     * Logout
     * This endpoint lets you to logout
     * @queryParam token string required User's token.
     * @responseFile storage/responses/auth/logout.json
     * @return mixed
     */
    public function logout()
    {
        return $this->auth->logout();

    }


    /**
     * Refresh token
     * This endpoint lets you to refresh token to user
     * @queryParam token string required User's token.
     * @responseFile storage/responses/auth/refresh.json
     * @return mixed
     */
    public function refresh()
    {
        return $this->auth->refresh();

    }

    /**
     * Update user
     * This endpoint lets you to update user's information
     * @responseFile storage/responses/auth/update.json
     * @param UpdateRequest $request
     * @return mixed
     */
    public function update(UpdateRequest $request)
    {
        return $this->auth->update();

    }

    /**
     * Request forget password
     * This endpoint lets you to update request forget password OTP
     * @unauthenticated
     * @responseFile storage/responses/auth/request_forget_password.json
     * @param RequestForgetPasswordRequest $request
     * @return mixed
     */
    public function requestForgetPassword(RequestForgetPasswordRequest $request)
    {
        return $this->auth->forgetPassword();
    }

    /**
     * Forget password
     * This endpoint lets you to update user password with OTP validation
     * @unauthenticated
     * @responseFile storage/responses/auth/forget_password.json
     * @param ForgetPasswordRequest $request
     * @return mixed
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        return $this->auth->resetPassword();
    }

    /**
     * Request email validation
     * This endpoint lets you to request email validation via OTP
     * @unauthenticated
     * @responseFile storage/responses/auth/validate_email.json
     * @return mixed
     */
    public function requestEmailValidation()
    {
        return $this->auth->requestEmailValidation();
    }

    /**
     * Validate email
     * This endpoint lets you to validate email using otp code
     * @responseFile storage/responses/auth/validate_email.json
     * @param ValidateEmailRequest $request
     * @return mixed
     */
    public function validateEmail(ValidateEmailRequest $request)
    {
        return $this->auth->validateEmail();

    }

}

