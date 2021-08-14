<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'origin',
        'detail',
        'archived',
        'area_id',
        'procedure_type_id',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function procedure_type()
    {
        return $this->belongsTo(ProcedureType::class);
    }

    public function procedure_flows()
    {
        return $this->hasMany(ProcedureFlow::class)->withTimestamps();
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = mb_strtoupper($value);
    }
}
