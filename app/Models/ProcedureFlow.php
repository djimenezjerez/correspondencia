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
        'from_area',
        'to_area',
        'user_id',
    ];

    public $timestamps = true;

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function flowed_from_area()
    {
        return $this->belongsTo(Area::class, 'from_area');
    }

    public function flowed_to_area()
    {
        return $this->belongsTo(Area::class, 'to_area');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
