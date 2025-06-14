<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected $fillable = [
        'question_id',
        'responder_id',
        'response_data',
    ];

    /**
     * Get the responder that owns the answer.
    */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(Responder::class, 'foreign_key');
    }

    /**
     * Get the question that owns the answer.
    */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
