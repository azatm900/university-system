<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class AuditService
{
    /**
     * The audit_logs table stores {old, new, description} inside a single
     * JSON column named `changes`. This method packs the three logical fields
     * into that one column so callers can keep the flat API.
     */
    public function log(
        string $action,
        ?Model $subject = null,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null,
    ): ?AuditLog {
        try {
            return AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'model_type' => $subject ? get_class($subject) : null,
                'model_id'   => $subject?->getKey(),
                'changes'    => [
                    'old'         => $oldValues ?: null,
                    'new'         => $newValues ?: null,
                    'description' => $description,
                ],
                'ip_address' => Request::ip(),
                'user_agent' => (string) Request::userAgent(),
            ]);
        } catch (Throwable) {
            return null;
        }
    }

    public function created(Model $model, ?string $desc = null): ?AuditLog
    {
        return $this->log('created', $model, [], $model->getAttributes(), $desc ?? "Created {$this->label($model)}");
    }

    public function updated(Model $model, array $old, ?string $desc = null): ?AuditLog
    {
        return $this->log('updated', $model, $old, $model->getAttributes(), $desc ?? "Updated {$this->label($model)}");
    }

    public function deleted(Model $model, ?string $desc = null): ?AuditLog
    {
        return $this->log('deleted', $model, $model->getAttributes(), [], $desc ?? "Deleted {$this->label($model)}");
    }

    public function forceDeleted(Model $model, ?string $desc = null): ?AuditLog
    {
        return $this->log('force_deleted', $model, $model->getAttributes(), [], $desc ?? "Force-deleted {$this->label($model)}");
    }

    public function login(User $user): ?AuditLog
    {
        return $this->log('login', $user, [], [], "User {$user->name} logged in");
    }

    public function logout(User $user): ?AuditLog
    {
        return $this->log('logout', $user, [], [], "User {$user->name} logged out");
    }

    protected function label(Model $model): string
    {
        return class_basename($model) . '#' . $model->getKey();
    }
}