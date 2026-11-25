<?php

namespace App\Models;

use App\Enums\ResultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'coursework_score',
        'midterm_score',
        'final_score',
        'total_score',
        'grade',
        'grade_points',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'coursework_score' => 'decimal:2',
            'midterm_score'    => 'decimal:2',
            'final_score'      => 'decimal:2',
            'total_score'      => 'decimal:2',
            'grade_points'     => 'decimal:2',
            'status'           => ResultStatus::class,
            'published_at'     => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function scopePassed($query)
    {
        return $query->where('status', ResultStatus::Passed->value);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', ResultStatus::Failed->value);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->whereHas(
            'registration',
            static fn ($q) => $q->where('student_id', $studentId)
        );
    }

    /**
     * Course through registration (accessor).
     */
    protected function course(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(
            fn () => $this->registration?->course
        );
    }
}