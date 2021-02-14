<?php


namespace App\Repositories\Expert;


use App\Enums\BookingDuration;
use App\Enums\Days;
use App\Enums\UserType;
use App\Interfaces\Auth\AuthInterface;
use App\Interfaces\Expert\ExpertInterface;
use App\Models\Expert;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\FileManagement;
use App\Transformers\BookingTransformer;
use App\Transformers\ExpertTransformer;
use App\Transformers\TimeAvailableTransformer;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Timer\Duration;
use stdClass;
use Tzsk\Otp\Facades\Otp;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\Translation\t;


class ExpertRepository implements ExpertInterface
{
    use  FileManagement, ApiResponse;

    protected $model;

    public function __construct(Expert $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        $experts = $this->model::with('user');
        return $this->showAll($experts, new ExpertTransformer());
    }

    public function show(Expert $expert)
    {
        return $this->showOne($expert, new ExpertTransformer());
    }

    //todo store status
    public function store()
    {
        $data = $this->uploadImageAndAppendImageNameToRequest(User::$ImagesDir);
        $user = new User($data);
        $user->user_type = UserType::EXPERT;
        $user->save();
        $expert = new Expert($data);
        $expert->user_id = $user->id;
        $expert->save();
        return $this->successMessage("The expert added successfully.");
    }

    //todo update status
    public function update(Expert $expert)
    {
        $expertData = request()->only(['job', 'country', 'time_zone']);
        $expert->fill($expertData)->save();
        $user = $expert->user;
        $oldImageName = $user->image_name;
        $data = $this->uploadImageAndAppendImageNameToRequest(User::$ImagesDir);
        $isEmptyOrNullOldImageName = $this->IsNullOrEmptyString($oldImageName);
        $user->fill($data)->save();
        $isChanged = ($oldImageName != $user->image_name) ? true : false;
        if (!$isEmptyOrNullOldImageName && $isChanged) {
            Storage::delete(User::$ImagesDir . ' / ' . $oldImageName);
        }
        return $this->successMessage("Expert's information updated successfully");
    }

    public function delete(Expert $expert)
    {
        //todo delete image and delete user by observe
        $expert->delete();
        $expert->user->delete();
        return $this->successMessage("Expert's information deleted successfully");
    }

    public function getAvailabilityTime(Expert $expert)
    {
        $dateBooking = request('date');
        $durationBookings = $expert->bookings()->whereDate('start_at', $dateBooking)->sum('duration');
        //we suppose work every day
        $workHours = $expert->workHours->firstWhere('day', '=', Days::ALL);

        $from = $workHours->from;
        $to = $workHours->to;;
        $calculateWorkHoursInDay = (strtotime($to) - strtotime($from)) / 60;
        $availableWorkHoursInDay = $calculateWorkHoursInDay - $durationBookings;
        $availableDuration = BookingDuration::getValues();
        foreach ($availableDuration as $key => $duration) {
            if ($availableWorkHoursInDay < $duration) {
                unset($availableDuration[$key]);
            }
        }
        $timesAvailable = [];
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $dateBooking . ' ' . $from);
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $dateBooking . ' ' . $to);

        $bookings = $expert->bookings()->whereDate('start_at', $dateBooking)->get(['start_at', 'finish_at']);

        foreach ($availableDuration as $key => $duration) {
            $newFrom = $from->copy();
            $newTo = $newFrom->copy()->addMinutes($duration);
            if (($to >= $newFrom) && ($to >= $newTo)) {
                $timeSlots = [];
                while (($to >= $newFrom) && ($to >= $newTo)) {
                    if ($this->checkTimeSlot($bookings, $newFrom, $newTo)) {
                        array_push($timeSlots, [
                            'from' => $newFrom->format('H:i:s'),
                            'to' => $newTo->format('H:i:s'),
                        ]);
                    }
                    $newFrom->addMinutes(15);
                    $newTo = $newFrom->copy()->addMinutes($duration);
                }
                if (!empty($timeSlots)) {
                    $timeAvailable = new stdClass();
                    $timeAvailable->duration = $duration;
                    $timeAvailable->timeSlots = $timeSlots;
                    array_push($timesAvailable, $timeAvailable);
                }
            }
        }

        return $this->transformData($timesAvailable, new TimeAvailableTransformer());

    }

    function checkTimeSlot($bookings, $from, $to)
    {
        foreach ($bookings as $booking) {
            if (
                ($from < $booking->start_at) && ($to > $booking->start_at) ||
                ($from < $booking->finish_at) && ($to > $booking->finish_at) ||
                ($from >= $booking->start_at) && ($to <= $booking->finish_at)

            ) {
                return false;
            }
        }
        return true;
    }
}
