<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserListResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controlador de usuarios (admin).
 */
class UserController extends Controller
{
    /**
     * Lista usuarios para filtro (apenas admin).
     */
    public function index(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        $query = User::query()->orderBy('name');

        if ($request->boolean('admin_only')) {
            $query->role('admin');
        }

        $search = $request->query('search');
        if (is_string($search) && $search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
            });
        }

        $users = $query->limit(50)->get(['id', 'name', 'email']);

        return UserListResource::collection($users)->additional([
            'message' => 'Users retrieved successfully.',
        ]);
    }
}
