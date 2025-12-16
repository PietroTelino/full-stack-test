<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Features;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\HasFeatures;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Cashier\Billable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Lib\Billing\ValidSubscription;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasApiTokens;
    use HasProfilePhoto;
    use HasFeatures;
    use Impersonate;
    use SoftDeletes;
    use ValidSubscription;
    use HasTeams;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::deleting(function (self $model) {
            $model->attributes['email'] = $model->attributes['email'].'.del-'.now()->unix();
            $model->saveQuietly();
            $model->ownedTeams()->each(function ($team) {
                $team->purge();
            });
        });
    }

    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     * @return bool
     */
    public function canImpersonate()
    {
        return Features::check(request(), 'admin');
    }
}
