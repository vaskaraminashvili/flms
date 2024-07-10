<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_id',
    ];


    protected $casts = [
        'id' => 'integer',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class);
    }
}
