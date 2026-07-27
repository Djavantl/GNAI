<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Lançada quando uma ação autenticada é proibida para o usuário atual.
 *
 * Use para regras de autorização/acesso que devem ser representadas como 403.
 */
class AccessDeniedException extends Exception {}
