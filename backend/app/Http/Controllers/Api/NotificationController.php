<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador de notificacoes do usuario.
 */
class NotificationController extends Controller
{
    /**
     * Lista notificacoes do usuario autenticado com paginacao.
     */
    public function index(Request $request): NotificationCollection
    {
        $query = $request->user()->notifications();

        if ($request->boolean('unread_only')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate($request->integer('per_page', 15));

        return (new NotificationCollection($notifications))->additional([
            'message' => 'Notifications retrieved successfully.',
        ]);
    }

    /**
     * Retorna a contagem de notificacoes nao lidas.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json([
            'message' => 'Unread count retrieved successfully.',
            'data' => ['count' => $count],
        ]);
    }

    /**
     * Marca uma notificacao como lida.
     */
    public function read(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);

        if (!$notification) {
            abort(404, 'Notification not found.');
        }

        $notification->markAsRead();

        return response()->json(null, 204);
    }
}
