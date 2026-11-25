<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The audit_logs table stores change payloads in a single JSON column: `changes`.
 * Shape:  { "old": {...}|null, "new": {...}|null, "description": "..."|null }
 *
 * IMPORTANT: accessors below read $this->attributes['changes'] directly, NOT
 * $this->changes. In Laravel 11/12, a legacy getXxxAttribute() cannot reliably
 * read a sibling cast attribute via $this->xxx — the cast is not applied inside
 * that scope. Using ->attributes bypasses this and is always correct.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // -----------------------------------------------------------------
    // Raw decoder used by all accessors
    // -----------------------------------------------------------------
    protected function decodedChanges(): ?array
    {
        $raw = $this->attributes['changes'] ?? null;
        if ($raw === null) return null;
        if (is_array($raw)) return $raw;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : null;
        }
        return null;
    }

    // -----------------------------------------------------------------
    // Accessors (backward-compatible API)
    // -----------------------------------------------------------------

    public function getOldValuesAttribute(): ?array
    {
        $changes = $this->decodedChanges();
        return $changes['old'] ?? null;
    }

    public function getNewValuesAttribute(): ?array
    {
        $changes = $this->decodedChanges();
        return $changes['new'] ?? null;
    }

    public function getDescriptionAttribute(): ?string
    {
        $changes = $this->decodedChanges();
        return $changes['description'] ?? null;
    }

    // -----------------------------------------------------------------
    // Relations & scopes
    // -----------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }
}