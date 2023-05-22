<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weather extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'main',
        'description',
        'temperature',
        'icon',
    ];

    /**
     * Associate Weather with event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
