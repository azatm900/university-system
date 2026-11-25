<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'semester_id',
        'invoice_number',
        'description',
        'amount',
        'discount',
        'total',
        'paid_amount',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'discount'    => 'decimal:2',
            'total'       => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'status'      => InvoiceStatus::class,
            'due_date'    => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [
            InvoiceStatus::Unpaid->value,
            InvoiceStatus::Partial->value,
        ]);
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [
            InvoiceStatus::Unpaid->value,
            InvoiceStatus::Partial->value,
        ])->whereDate('due_date', '<', now());
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}