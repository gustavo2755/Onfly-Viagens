<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controller base da aplicacao.
 */
abstract class Controller
{
    use AuthorizesRequests;
}
