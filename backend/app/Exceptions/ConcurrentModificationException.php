<?php

namespace App\Exceptions;

use App\Models\TravelOrder;
use RuntimeException;

/**
 * Excecao lancada quando outro admin ja alterou ou esta alterando o pedido.
 */
class ConcurrentModificationException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly TravelOrder $travelOrder
    ) {
        parent::__construct($message);
    }

    /**
     * Retorna o pedido no estado atual para incluir na resposta 409.
     */
    public function getTravelOrder(): TravelOrder
    {
        return $this->travelOrder;
    }
}
