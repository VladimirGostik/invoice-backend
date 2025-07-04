<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Knuckles\Scribe\Attributes\Group;
use Spatie\Permission\Models\Role;

#[Group('Role')]
class RoleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return RoleResource::collection(Role::all());
    }
}