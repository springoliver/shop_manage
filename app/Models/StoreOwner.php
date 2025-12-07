<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class StoreOwner extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_storeowner';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ownerid';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'emailid',
        'password',
        'profile_photo',
        'phone',
        'country',
        'address1',
        'address2',
        'state',
        'city',
        'zipcode',
        'dateofbirth',
        'accept_terms',
        'signupdate',
        'signupip',
        'signupby',
        'editdate',
        'editip',
        'editby',
        'status',
        'lastlogindate',
        'lastloginip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'dateofbirth' => 'date',
            'signupdate' => 'datetime',
            'editdate' => 'datetime',
            'lastlogindate' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the route key name for Laravel route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'ownerid';
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'ownerid';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the column name for the "username".
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->emailid;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->emailid;
    }

    /**
     * Get the stores owned by this store owner.
     */
    public function stores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Store::class, 'storeownerid', 'ownerid');
    }

    /**
     * Get the active stores owned by this store owner.
     */
    public function activeStores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Store::class, 'storeownerid', 'ownerid')
            ->where('status', 'Active');
    }
}

