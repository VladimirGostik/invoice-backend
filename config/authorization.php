<?php

use App\Enums\UserRoleEnum;

return [

    /*
    |--------------------------------------------------------------------------
    | Application permissions configuration
    |--------------------------------------------------------------------------
    */

    'permissions' => [
//        PermissionGroupEnum::ENTITIES->value => [
//            'view entities',
//            'view entity',
//            'create entity',
//            'update entity',
//            'delete entity',
//        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Application roles configuration
    |--------------------------------------------------------------------------
    */

    'roles' => [
        UserRoleEnum::SUPER_ADMIN->value => [

        ],
        UserRoleEnum::ADMIN->value => [

        ],
    ],

];
