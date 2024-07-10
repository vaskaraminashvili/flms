<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];


    protected $casts = [
        'id' => 'integer',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class , 'class_id');
    }
}
