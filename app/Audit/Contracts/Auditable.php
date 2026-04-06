<?php

namespace App\Audit\Contracts;

interface Auditable
{
    public static function auditLabels(): array;
    public static function auditFormatter(): string;
}
