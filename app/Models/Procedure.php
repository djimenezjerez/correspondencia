<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory, SoftDeletes;

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
        return $this->hasMany(ProcedureFlow::class);
    }

    public function file_uploads()
    {
        return $this->morphMany(FileUpload::class, 'uploadable');
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = trim(mb_strtoupper($value));
    }

    public function getHasFlowedAttribute()
    {
        return $this->procedure_flows()->count() > 0;
    }

    public function getOwnerAttribute()
    {
        return $this->area_id == auth()->user()->area_id;
    }
}
