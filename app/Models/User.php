<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\DoctorProfile;
use App\Models\DoctorSchedule;
use App\Models\DoctorUnavailability;
use App\Models\DoctorDelay;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            // Set default role if not set
            if (empty($model->role)) {
                $model->role = 'patient';
            }
            
            // Set default language if not set
            if (empty($model->language)) {
                $model->language = 'fr';
            }
        });
    }
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'email_verified_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];


    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'otp',
        'otp_expires_at',
        'otp_verified_at',
        'role',
        'language',
        'profile_photo_path',
        'is_active',
        'latitude',
        'longitude',
    ];

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->full_name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Check if the user is a patient.
     */
    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    /**
     * Check if the user is a doctor.
     */
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /**
     * Get the doctor's profiles (one doctor can have multiple affiliations).
     */
    public function doctorProfiles()
    {
        return $this->hasMany(DoctorProfile::class, 'user_id');
    }

    /**
     * Get the doctor's profile (backward compatibility - returns first profile).
     * @deprecated Use doctorProfiles() instead
     */
    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class, 'user_id');
    }

    /**
     * Get the doctor's schedules.
     */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    /**
     * Get the doctor's unavailabilities.
     */
    public function unavailabilities()
    {
        return $this->hasMany(DoctorUnavailability::class, 'doctor_id');
    }

    /**
     * Get the doctor's delays.
     */
    public function doctorDelays()
    {
        return $this->hasMany(DoctorDelay::class, 'doctor_id');
    }

    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * Récupérer les créneaux disponibles pour ce médecin sur une date donnée
     */
    public function getAvailableSlots(Carbon $date)
    {
        $schedules = $this->schedules()
            ->where('is_available', true)
            ->get();

        if ($schedules->isEmpty()) {
            return collect();
        }

        $dayOfWeek = strtolower($date->englishDayOfWeek);
        $schedule = $schedules->firstWhere('day_of_week', $dayOfWeek);

        if (!$schedule) {
            return collect();
        }

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $consultationTime = $this->doctorProfile->average_consultation_time ?? 30;

        $existingAppointments = $this->doctorAppointments()
            ->whereDate('appointment_date', $date->toDateString())
            ->whereIn('status', ['scheduled'])
            ->pluck('appointment_date')
            ->map(function ($dt) {
                return Carbon::parse($dt)->format('H:i');
            })
            ->toArray();

        $unavailabilities = $this->unavailabilities()
            ->whereDate('unavailable_date', $date->toDateString())
            ->get()
            ->map(function ($unavailability) {
                return [
                    'start' => Carbon::parse($unavailability->start_time)->format('H:i'),
                    'end' => Carbon::parse($unavailability->end_time)->format('H:i'),
                ];
            });

        $currentTime = $startTime->copy();
        $daySlots = [];

        while ($currentTime->addMinutes($consultationTime)->lte($endTime)) {
            $slotStart = $currentTime->copy()->subMinutes($consultationTime);
            $slotEnd = $currentTime->copy();
            $slotTime = $slotStart->format('H:i');

            $isInUnavailability = $unavailabilities->contains(function ($unavailability) use ($slotStart, $slotEnd) {
                $unavailableStart = Carbon::parse($unavailability['start']);
                $unavailableEnd = Carbon::parse($unavailability['end']);

                return $slotStart->lt($unavailableEnd) && $slotEnd->gt($unavailableStart);
            });

            if (!in_array($slotTime, $existingAppointments) && !$isInUnavailability) {
                $daySlots[] = [
                    'time' => $slotTime,
                    'formatted_time' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                    'timestamp' => $date->copy()->setTimeFrom($slotStart)->toDateTimeString(),
                    'date' => $date->toDateString(),
                    'day_name' => $date->translatedFormat('l'),
                ];
            }
        }

        if (empty($daySlots)) {
            return collect();
        }

        return collect([
            [
                'date' => $date->toDateString(),
                'day_name' => $date->translatedFormat('l'),
                'slots' => $daySlots,
            ],
        ]);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a receptionist.
     */
    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    /**
     * Get the departments managed by the receptionist (health center).
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'reception_id');
    }
    
    /**
     * Get the health center (reception) of the user.
     */
    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class, 'health_center_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
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
}
