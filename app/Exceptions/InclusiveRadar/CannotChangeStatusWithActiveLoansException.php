<?php

namespace App\Exceptions\InclusiveRadar;

use Exception;

class CannotChangeStatusWithActiveLoansException extends Exception
{
    protected $message = 'Não é possível alterar o status enquanto houver empréstimos ativos.';
}
