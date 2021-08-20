<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename',
        'path',
        'user_id',
    ];

    public $timestamps = true;

    public function attachable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setFilenameAttribute($value)
    {
        $this->attributes['filename'] = trim(mb_strtoupper($value));
    }

    public function setPathAttribute($value)
    {
        $this->attributes['path'] = trim(mb_strtolower($value));
    }

    public function getFullPathAttribute()
    {
        return $this->path . '/' . $this->filename;
    }
}
