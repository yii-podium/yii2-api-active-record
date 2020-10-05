<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Log Active Record.
 *
 * @property int                $id
 * @property int                $member_id
 * @property string             $action
 * @property int                $created_at
 * @property int                $updated_at
 * @property MemberActiveRecord $member
 */
class LogActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_log}}';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }

    public function getMember(): ActiveQuery
    {
        return $this->hasOne(MemberActiveRecord::class, ['id' => 'member_id']);
    }
}
