<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
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

    public function uploadable()
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
}
