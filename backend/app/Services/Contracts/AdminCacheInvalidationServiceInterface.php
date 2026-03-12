<?php

namespace App\Services\Contracts;

/**
 * Contrato para invalidacao de cache administrativo.
 */
interface AdminCacheInvalidationServiceInterface
{
    /**
     * Incrementa a versao de cache usada nas chaves admin.
     */
    public function bumpTravelOrdersAdminVersion(): void;
}
