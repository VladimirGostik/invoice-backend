<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission as SpatiePermission;

use Spatie\Permission\Guard;

class Permission extends SpatiePermission
{
    /**
     * Permission group relation
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    /**
     * Find or create permission by its name and group (and optionally guardName).
     */
    public static function findOrCreateInGroup(int $groupId, string $name, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermission([
            'name' => $name,
            'permission_group_id' => $groupId,
            'guard_name' => $guardName
        ]);

        if (!$permission) {
            return static::query()->create([
                'name' => $name,
                'permission_group_id' => $groupId,
                'guard_name' => $guardName
            ]);
        }

        return $permission;
    }
}