<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $table = 'pkl';
    protected $fillable = ['student_id', 'teacher_id', 'industry_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
