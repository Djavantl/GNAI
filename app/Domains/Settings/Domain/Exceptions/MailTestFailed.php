<?php

declare(strict_types=1);

namespace App\Domains\Settings\Domain\Exceptions;

use App\Shared\Domain\Exceptions\BusinessRuleException;

final class MailTestFailed extends BusinessRuleException {}
