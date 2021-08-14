<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureFlow extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'procedure_id',
        'area_id',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
