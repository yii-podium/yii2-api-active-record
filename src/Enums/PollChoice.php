<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Enums;

use Yii;

final class PollChoice extends BaseEnum
{
    public const SINGLE = 'single';
    public const MULTIPLE = 'multiple';

    public static function data(): array
    {
        return [
            self::SINGLE => Yii::t('podium.enum', 'poll.choice.single'),
            self::MULTIPLE => Yii::t('podium.enum', 'poll.choice.multiple'),
        ];
    }
}
