<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'identity_card',
        'username',
        'password',
        'email',
        'address',
        'phone',
        'area_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function procedure_flows()
    {
        return $this->hasMany(ProcedureFlow::class);
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim(mb_strtoupper($value));
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = trim(mb_strtoupper($value));
    }

    public function setIdentityCardAttribute($value)
    {
        $this->attributes['identity_card'] = trim(mb_strtoupper($value));
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = trim(mb_strtoupper($value));
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(mb_strtolower($value));
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = trim(mb_strtoupper($value));
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->last_name;
    }
}
