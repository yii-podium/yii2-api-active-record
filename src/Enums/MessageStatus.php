<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Enums;

use Yii;

final class MessageStatus extends BaseEnum
{
    public const NEW = 'new';
    public const READ = 'read';

    public static function data(): array
    {
        return [
            self::NEW => Yii::t('podium.enum', 'message.status.new'),
            self::READ => Yii::t('podium.enum', 'message.status.read'),
        ];
    }
}
