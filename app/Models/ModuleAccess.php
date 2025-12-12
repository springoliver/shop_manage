<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleAccess extends Model
{
    protected $table = 'stoma_module_access';
    protected $primaryKey = 'accessid';
    public $timestamps = false;
    
    protected $fillable = [
        'storeid',
        'usergroupid',
        'moduleid',
        'level',
        'insertdate',
        'insertip',
        'editdate',
        'editip',
    ];
    
    protected $casts = [
        'insertdate' => 'datetime',
        'editdate' => 'datetime',
    ];
    
    public function module()
    {
        return $this->belongsTo(Module::class, 'moduleid', 'moduleid');
    }
}

