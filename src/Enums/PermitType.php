<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Enums;

final class PermitType
{
    public const CREATE = 0b1;
    public const READ = 0b10;
    public const UPDATE = 0b100;
    public const DELETE = 0b1000;
}
