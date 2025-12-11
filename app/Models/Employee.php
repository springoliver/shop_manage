<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_employee';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'employeeid';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The guard name for spatie/permission.
     *
     * @var string
     */
    protected $guard_name = 'employee';

    /**
     * The username field for authentication.
     *
     * @var string
     */
    public function getAuthIdentifierName()
    {
        return 'emailid';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'storeid',
        'usergroupid',
        'departmentid',
        'roster_week_hrs',
        'roster_day_hrs',
        'break_every_hrs',
        'break_min',
        'paid_break',
        'display_hrs_hols',
        'holiday_percent',
        'holiday_day_entitiled',
        'firstname',
        'lastname',
        'username',
        'emailid',
        'emptaxnumber',
        'empnationality',
        'empjoindate',
        'empbankdetails1',
        'empbankdetails2',
        'emplogin_code',
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
        'payment_method',
        'sallary_method',
        'pay_rate_hour',
        'signupdate',
        'signupip',
        'signupby',
        'editdate',
        'editip',
        'editby',
        'status',
        'lastlogindate',
        'lastloginip',
        'pay_rate_week',
        'pay_rate_year',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'empjoindate' => 'date',
            'dateofbirth' => 'date',
            'signupdate' => 'datetime',
            'editdate' => 'datetime',
            'lastlogindate' => 'datetime',
            'holiday_percent' => 'decimal:0',
            'holiday_day_entitiled' => 'decimal:0',
            'pay_rate_hour' => 'decimal:2',
            'pay_rate_week' => 'decimal:2',
            'pay_rate_year' => 'decimal:2',
        ];
    }

    /**
     * Get the store that owns the employee.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the user group that the employee belongs to.
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'usergroupid', 'usergroupid');
    }

    /**
     * Get the department that the employee belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentid', 'departmentid');
    }

    /**
     * Get the name of the "remember me" token column.
     * Stoma_employee table doesn't have remember_token, so return null.
     *
     * @return string|null
     */
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * Get the value of the "remember me" token.
     * Stoma_employee table doesn't have remember_token, so return null.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the value of the "remember me" token.
     * Stoma_employee table doesn't have remember_token, so do nothing.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Do nothing - stoma_employee table doesn't have remember_token
    }
}
