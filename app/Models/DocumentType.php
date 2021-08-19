<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
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

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim(mb_strtoupper($value));
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = trim(mb_strtoupper($value));
    }
}
