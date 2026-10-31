<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Exceptions;

use App\Shared\Domain\Exceptions\BusinessRuleException;

/** Regra de negócio violada no domínio de inclusão. */
abstract class InclusionException extends BusinessRuleException {}
