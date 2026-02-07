<?php

namespace App\Exceptions\InclusiveRadar;

use Exception;

class CannotDeleteWithActiveLoans extends Exception
{
    protected $message = 'Não foi possível excluir. Este recurso ainda possui empréstimos pendentes.';
}
