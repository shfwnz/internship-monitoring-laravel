<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Industry extends Model
{
    use HasFactory;
    protected $table = 'industries';
    protected $fillable = [
        'name',
        'business_field_id',
        'address',
        'phone',
        'email',
        'website',
    ];

    public function pkl()
    {
        return $this->hasMany(Pkl::class);
    }

    public function business_field()
    {
        return $this->belongsTo(BusinessField::class);
    }
}
