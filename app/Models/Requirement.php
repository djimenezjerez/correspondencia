<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;

    public function procedure_types()
    {
        return $this->morphedByMany(ProcedureType::class, 'requirable')->withTimestamps();
    }

    public function procedures()
    {
        return $this->morphedByMany(Procedure::class, 'requirable')->withPivot('validated')->withTimestamps();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim(mb_strtoupper($value));
    }

    public function getIsUsedAttribute()
    {
        return ($this->procedure_types()->count() > 0);
    }
}
