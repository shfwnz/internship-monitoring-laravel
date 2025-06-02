<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = ['nip'];

    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function internships()
    {
        return $this->hasMany(Internship::class);
    }
}
