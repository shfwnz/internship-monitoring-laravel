<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessField extends Model
{
    use HasFactory;
    protected $table = 'business_fields';
    protected $fillable = ['name'];

    public function industries()
    {
        return $this->hasMany(Industry::class);
    }
}
