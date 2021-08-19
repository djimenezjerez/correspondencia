<?php

namespace App\Models;

use Carbon\Carbon;
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
        $this->attributes['name'] = trim(mb_strtoupper($value));
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = trim(mb_strtoupper($value));
    }

    public function getTotalProceduresAttribute()
    {
        return $this->procedures()->count();
    }

    public function getNextCodeAttribute()
    {
        $year = Carbon::now()->year;
        $counter = $this->counter;
        do {
            $counter = $counter + 1;
            $code = implode('/', ['ASCINALSS', auth()->user()->area->code, $this->code, $counter, $year]);
        } while (Procedure::where('code', $code)->exists());
        return $code;
    }
}
