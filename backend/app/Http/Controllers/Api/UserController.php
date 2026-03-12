<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserListResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return UserListResource::collection($users)->additional([
            'message' => 'Users retrieved successfully.',
        ]);
    }
}
