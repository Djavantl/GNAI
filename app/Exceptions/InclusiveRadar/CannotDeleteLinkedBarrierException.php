<?php

namespace App\Exceptions\InclusiveRadar;

use Exception;

class CannotDeleteLinkedBarrierException extends Exception
{
    protected $message =
        'Não foi possível excluir. Este recurso está vinculado a barreiras ativas.';
}
