<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * Trait para formatacao de datas no padrao brasileiro (dd/mm/yyyy).
 */
trait FormatsDateBr
{
    /**
     * Formata data no padrao BR (dd/mm/yyyy).
     */
    protected function formatDateBr(mixed $date): ?string
    {
        return $date ? Carbon::parse($date)->format('d/m/Y') : null;
    }

    /**
     * Formata data/hora no padrao BR (dd/mm/yyyy H:i).
     */
    protected function formatDateTimeBr(mixed $date): ?string
    {
        return $date ? Carbon::parse($date)->format('d/m/Y H:i') : null;
    }
}
