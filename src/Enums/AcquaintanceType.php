<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Enums;

use Yii;

final class AcquaintanceType extends BaseEnum
{
    public const FRIEND = 'friend';
    public const IGNORE = 'ignore';

    public static function data(): array
    {
        return [
            self::FRIEND => Yii::t('podium.enum', 'acquaintance.type.friend'),
            self::IGNORE => Yii::t('podium.enum', 'acquaintance.type.ignore'),
        ];
    }
}
