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
        'pending',
        'user_id',
        'verified',
        'counter',
    ];

    public $timestamps = true;

    public function requirements()
    {
        return $this->morphToMany(Requirement::class, 'requirable')->withPivot('validated')->withTimestamps();
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function procedure_type()
    {
        return $this->belongsTo(ProcedureType::class);
    }

    public function procedure_flows()
    {
        return $this->hasMany(ProcedureFlow::class);
    }

    public function latest_flow()
    {
        return $this->hasOne(ProcedureFlow::class)->latest();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = trim(mb_strtoupper($value));
    }

    public function getHasFlowedAttribute()
    {
        if ($this->procedure_flows()->count() == 1) {
            $flow = $this->procedure_flows()->first();
            if ($flow->from_area == $flow->to_area) {
                return false;
            }
        }
        return true;
    }

    public function getValidatedAttribute()
    {
        return $this->requirements()->wherePivot('validated', false)->count() == 0;
    }

    public function getOwnerAttribute()
    {
        return $this->area_id == auth()->user()->area_id;
    }
}
