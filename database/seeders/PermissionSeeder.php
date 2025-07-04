<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Fix permission groups names
        foreach (config('authorization.permissions') as $groupName => $permissions) {
            $group = PermissionGroup::where('name', 'app.'.$groupName)->first();
            if ($group) {
                $group->name = __('app.'.$groupName);
                $group->save();
            }
        }
        // Create permissions
        foreach (config('authorization.permissions') as $groupName => $permissions) {
            $group = PermissionGroup::firstOrCreate(['name' => __('app.'.$groupName)]);
            foreach ($permissions as $permission) {
                Permission::findOrCreateInGroup($group->id, $permission);
            }
        }

        // Create roles
        foreach (config('authorization.roles') as $roleName => $permissions) {
            Role::findOrCreate($roleName)
                ->syncPermissions($permissions);
        }
    }
}
