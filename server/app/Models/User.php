<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'firstName',
    'lastName',
    'middleName',
    'age',
    'birthDate',
    'birthProvince',
    'subdivision',
    'street',
    'barangay',
    'role' ,
    'city',
    'province',
    'postalCode',
    'mobileNumber',
    'email',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'userId');
    }
    public function faculty(): HasOne
    {
        return $this->hasOne(Faculty::class, 'userId');
    }
    public function awards(): HasMany
    {
        return $this->hasMany(Award::class, 'userId');
    }
    public function educationalBackgrounds(): HasMany
    {
        return $this->hasMany(EducationalBackground::class, 'userId');
    }
    public function eduBackground(): HasMany
    {
        return $this->hasMany(EducationalBackground::class, 'userId');
    }
    public function organizations(): HasMany
    {
        return $this->hasMany(UserOrganization::class, 'userId');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'int'
        ];
    }
}