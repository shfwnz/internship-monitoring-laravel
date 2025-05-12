<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasRoles, HasFactory;
    protected $fillable = ['nip'];

    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function pkl()
    {
        return $this->hasMany(Pkl::class);
    }
}
