<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Thread Group Active Record.
 *
 * @property int                $thread_id
 * @property int                $group_id
 * @property ThreadActiveRecord $thread
 * @property GroupActiveRecord  $group
 */
class ThreadGroupActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_thread_group}}';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function getThread(): ActiveQuery
    {
        return $this->hasOne(ThreadActiveRecord::class, ['id' => 'thread_id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(GroupActiveRecord::class, ['id' => 'group_id']);
    }
}
