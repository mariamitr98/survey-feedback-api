<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'survey_id',
        'type',
        'question_text',
    ];

    /**
     * Get the survey that owns the question.
    */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'foreign_key');
    }

    /**
     * Get the answer associated with the question.
    */
    public function answer(): HasOne
    {
        return $this->hasOne(Answer::class);
    }
}
