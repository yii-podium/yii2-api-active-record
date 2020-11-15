<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Member Group Active Record.
 *
 * @property int                  $member_id
 * @property int                  $group_id
 * @property CategoryActiveRecord $category
 * @property GroupActiveRecord    $group
 */
class MemberGroupActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_member_group}}';
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

    public function getMember(): ActiveQuery
    {
        return $this->hasOne(MemberActiveRecord::class, ['id' => 'member_id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(GroupActiveRecord::class, ['id' => 'group_id']);
    }
}
