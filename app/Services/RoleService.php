<?php
namespace App\Services;

use App\Models\Trust\Role;
use App\User;

class RoleService
{
    /**
     * Add role for user
     * @param User   $user
     * @param string $roleName
     */
    public static function add(User $user, $roleName)
    {
        if (!$roleName) {
            throwError('Please insert role name', 422);
        }
        if (!empty($roleName) && !in_array($roleName, Role::roles())) {
            throwError('Some thing went wrong!', 500);
        }
        // find or create role admin
        $role = Role::firstOrCreate(['name' => $roleName]);

        return $user->roles()->attach($role);
    }

    /**
     * Sync role of user
     * @param  User   $user
     * @param  string $roleName
     * @return Illuminate\Http\Response
     */
    public static function sync(User $user, $roleName)
    {
        if (!$roleName) {
            throwError('Please insert role name', 422);
        }
        if (!empty($roleName) && !in_array($roleName, Role::roles())) {
            throwError('Some thing went wrong!', 500);
        }
        // find or create role admin
        $role = Role::firstOrCreate(['name' => $roleName]);

        return $user->roles()->sync($role);
    }
}
