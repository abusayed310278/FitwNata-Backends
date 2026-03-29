<?php
use Carbon\Carbon;
use App\Models\User;
use App\Models\Campus;
use Illuminate\Support\Str;
use App\Models\Subscription;
use App\Models\PayoutRequest;
use App\Models\EmployeeCampus;
use Spatie\Permission\Models\Role;
use App\Models\DeliveryTaskHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;




function sendMail($data)
{
    Mail::send($data['view'], ['data' => $data['data']], function ($message) use ($data) {
        $message->to($data['to'], $data['data']['name'] ?? '')
        ->from($data['mail_from'] ?? 'no-reply@fitwnata.com', $data['project'] ?? 'FitwNata')
          ->subject($data['subject']);
    });
}

function uploadFile($file,$path = null)
{
    // Define the destination path inside the 'public/uploads' directory
    $d = $path ? 'uploads/'.$path : 'uploads';
    $destinationPath = public_path($d);
    // Generate a unique file name to prevent overwriting
    $fileName = time() . '_' . $file->getClientOriginalName();

    // Move the file to the public/uploads directory
    $file->move($destinationPath, $fileName);

    if($path)
    {
        return asset('uploads/'.$path.'/' . $fileName);
    }

    // Return the public URL to the uploaded file
    return asset('uploads/' . $fileName);
}


if (!function_exists('getCurrentCycle')) {
    function getCurrentCycle(): string
    {
        $year = now()->year;
        $month = now()->month;

        if ($year == 2025 && $month >= 7) {
            return "2025-H2"; // special case (launch Jul–Dec 2025)
        }

        if ($month >= 1 && $month <= 6) {
            return "{$year}-H1"; // Jan–Jun
        } else {
            return "{$year}-H2"; // Jul–Dec
        }
    }
}

if (!function_exists('getAddressFromLatLng')) {
    function getAddressFromLatLng($lat, $lng)
    {
        $apiKey = config('services.google.api');
        \Log::info($apiKey);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";

        $response = file_get_contents($url);
        $json = json_decode($response, true);

        if (!empty($json['results'][0]['formatted_address'])) {
            return $json['results'][0]['formatted_address'];
        }

        return 'no location';
    }
}



if (!function_exists('pendingPayoutCount')) {
    function pendingPayoutCount()
    {
        return PayoutRequest::where('status','!=', 'paid')->where('status','!=', 'denied')->count();
    }
}


if (!function_exists('genderOptions')) {
    function genderOptions(): array
    {
        return ['male', 'female', 'other'];
    }
}

if (!function_exists('healthConditionsOptions')) {
    function healthConditionsOptions(): array
    {
        return ['diabetes', 'hypertension', 'heart_disease', 'arthritis', 'none'];
    }
}


if (!function_exists('equipmentAvailabilityOptions')) {
    function equipmentAvailabilityOptions(): array
    {
        return ['dumbbells', 'barbell', 'yoga_mat', 'resistance_bands', 'none'];
    }
}

if (!function_exists('preferredWorkoutTypesOptions')) {
    function preferredWorkoutTypesOptions(): array
    {
        return ['abs', 'cardio', 'toning', 'strength', 'yoga', 'pilates'];
    }
}

if (!function_exists('trainingLocationOptions')) {
    function trainingLocationOptions(): array
    {
        return ['home', 'gym', 'outdoors', 'no_preference'];
    }
}

if (!function_exists('fitnessGoalsOptions')) {
    function fitnessGoalsOptions(): array
    {
        return ['weight_loss', 'muscle_gain', 'endurance', 'flexibility', 'general_fitness'];
    }
}

if (!function_exists('planTypeOptions')) {
    function planTypeOptions(): array
    {
        return ['free', 'premium'];
    }
}

function userActiveSubscription($userId)
{
    return Subscription::with('plan')
        ->where('user_id', $userId)
        ->where('status', 'active')
        ->first();
}

function uploadImage($file, $folder = 'uploads')
{
    if (!$file || !$file->isValid()) {
        return null;
    }

    $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

    $destination = public_path($folder);

    if (!file_exists($destination)) {
           mkdir($destination, 0755, true);
    }

    $file->move($destination, $filename);

    return $folder . '/' . $filename;
}