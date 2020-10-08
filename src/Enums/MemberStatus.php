<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Enums;

use Yii;

final class MemberStatus extends BaseEnum
{
    public const ACTIVE = 'active';
    public const BANNED = 'banned';

    public static function data(): array
    {
        return [
            self::ACTIVE => Yii::t('podium.enum', 'member.status.active'),
            self::BANNED => Yii::t('podium.enum', 'member.status.banned'),
        ];
    }
}
