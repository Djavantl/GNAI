<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Exceptions;

use App\Shared\Domain\Exceptions\BusinessRuleException;

abstract class InclusiveRadarException extends BusinessRuleException {}
