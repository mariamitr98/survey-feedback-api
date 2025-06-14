<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    /**
     * Get the questions for the survey.
    */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
