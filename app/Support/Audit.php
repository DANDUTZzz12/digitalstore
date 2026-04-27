<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Audit
{
    /**
     * Log sebuah event ke audit_logs.
     *
     * @param  array<string,mixed>|null  $changes
     */
    public static function log(
        string $event,
        ?Model $subject = null,
        ?array $changes = null,
    ): void {
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id' => $subject?->getKey(),
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 500),
        ]);
    }
}
