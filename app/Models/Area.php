<?php

namespace App\Models;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function procedure_flows()
    {
        return $this->hasMany(ProcedureFlow::class)->withTimestamps();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = mb_strtoupper($value);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = mb_strtoupper($value);
    }
}
