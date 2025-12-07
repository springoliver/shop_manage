<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreEmployee extends Model
{
    use HasFactory;

    protected $table = 'stoma_employee';
    protected $primaryKey = 'employeeid';
    public $incrementing = true;
    public $timestamps = false;

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

    protected $casts = [
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

    /**
     * Get the store that owns the employee.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the user group that the employee belongs to.
     */
    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'usergroupid', 'usergroupid');
    }

    /**
     * Get the department that the employee belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentid', 'departmentid');
    }
}

