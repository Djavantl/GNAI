<?php

declare(strict_types=1);

namespace App\Domains\Backup\Domain\Exceptions;

use App\Shared\Domain\Exceptions\BusinessRuleException;

final class BackupOperationFailed extends BusinessRuleException
{
}
