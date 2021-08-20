<?php

namespace App\Models;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
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
        'group',
        'order',
        'role_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function procedure_flows_origin()
    {
        return $this->hasMany(ProcedureFlow::class, 'from_area');
    }

    public function procedure_flows_destiny()
    {
        return $this->hasMany(ProcedureFlow::class, 'to_area');
    }

    public function file_uploads()
    {
        return $this->morphMany(FileUpload::class, 'uploadable');
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
