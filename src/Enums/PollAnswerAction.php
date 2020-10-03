<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Enums;

use Yii;

final class PollAnswerAction extends BaseEnum
{
    public const ADD = 'add';
    public const EDIT = 'edit';
    public const REMOVE = 'remove';

    public static function data(): array
    {
        return [
            self::ADD => Yii::t('podium.enum', 'poll.answer.action.add'),
            self::EDIT => Yii::t('podium.enum', 'poll.answer.action.edit'),
            self::REMOVE => Yii::t('podium.enum', 'poll.answer.action.remove'),
        ];
    }
}
