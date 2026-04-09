<?php

namespace App\Exceptions;

use Exception;

/**
 * Lançada quando uma regra de negócio impede a execução de uma ação.
 *
 * Use quando o usuário tenta fazer algo que o sistema não permite
 * por razões de negócio. Não é um bug, é uma restrição esperada.
 */
class BusinessRuleException extends Exception {}
