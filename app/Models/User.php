<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserProfile;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Billable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected static function booted(): void
    {
        // This event listener will be called after a new user is created.
        static::created(function (User $user) {
            if (!app()->runningInConsole()) {
                return;
            }
            if (!Role::where('name', 'admin')->exists()) {
                return;
            }
            if (self::count() === 1) {
                $user->assignRole('admin');
            }
        });
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // If null or empty, use default avatar
                if (empty($value)) {
                    return asset('assets/images/default.png');
                }

                // If already a full URL (starts with http or https), return as-is
                if (preg_match('/^https?:\/\//', $value)) {
                    return $value;
                }

                // Otherwise, it's a relative path from /public, so prefix with asset()
                return asset($value);
            }
        );
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }


    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the articles written by the user (as a coach/admin).
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Get the appointments for the user as a client.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    /**
     * Get the appointments for the user as a client.
     */
    public function appointmentsAsClient()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    /**
     * Get the appointments for the user as a coach.
     */
    public function appointmentsAsCoach()
    {
        return $this->hasMany(Appointment::class, 'coach_id');
    }

    /**
     * Get the availability schedule for the user as a coach.
     */
    public function availabilities()
    {
        return $this->hasMany(CoachAvailabilities::class, 'coach_id');
    }

    /**
     * Get the progress journals for the user.
     */
    public function progressJournals()
    {
        return $this->hasMany(ProgressJournal::class);
    }

    /**
     * Get the progress journals for the user.
     */
    public function journals()
    {
        return $this->hasMany(ProgressJournal::class);
    }

    /**
     * Get the measurements for the user.
     */
    public function measurements()
    {
        return $this->hasMany(UserMeasurement::class);
    }

    /**
     * Get the shop orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }

    public function logs()
    {
        return $this->hasMany(UserLog::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function workoutAssignments()
    {
        return $this->hasMany(UserWorkoutAssignment::class);
    }
}
