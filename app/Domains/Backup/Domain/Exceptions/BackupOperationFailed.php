<?php

declare(strict_types=1);

namespace App\Domains\Backup\Domain\Exceptions;

use App\Exceptions\BusinessRuleException;

final class BackupOperationFailed extends BusinessRuleException
{
}
