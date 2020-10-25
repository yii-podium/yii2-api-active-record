<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Forum Group Active Record.
 *
 * @property int               $forum_id
 * @property int               $group_id
 * @property ForumActiveRecord $forum
 * @property GroupActiveRecord $group
 */
class ForumGroupActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_forum_group}}';
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

    public function getForum(): ActiveQuery
    {
        return $this->hasOne(ForumActiveRecord::class, ['id' => 'forum_id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(GroupActiveRecord::class, ['id' => 'group_id']);
    }
}
