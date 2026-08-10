<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Exceptions;

use App\Shared\Domain\Exceptions\BusinessRuleException;

final class InvalidProfile extends BusinessRuleException {}
