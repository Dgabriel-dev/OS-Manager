<?php

namespace App\Services;

use Illuminate\Http\Request;

class AuditService
{
    public function log(
        $user,
        string $event,
        $auditable,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null,
    ): void {
        $auditData = [
            'user_id' => $user?->id,
            'event' => $event,
            'auditable_type' => is_object($auditable) ? get_class($auditable) : $auditable,
            'auditable_id' => is_object($auditable) ? $auditable->getKey() : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ];

        \DB::table('audits')->insert($auditData);
    }
}
