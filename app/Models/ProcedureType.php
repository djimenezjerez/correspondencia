<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureType extends Model
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

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class);
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = mb_strtoupper($value);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = mb_strtoupper($value);
    }

    public function getTotalProceduresAttribute()
    {
        return $this->procedures()->count();
    }
}
