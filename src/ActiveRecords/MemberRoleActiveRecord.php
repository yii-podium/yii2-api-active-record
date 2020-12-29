<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Member Role Active Record.
 *
 * @property int                $member_id
 * @property int                $role_id
 * @property int                $permit
 * @property int                $created_at
 * @property int                $updated_at
 * @property RoleActiveRecord   $role
 * @property MemberActiveRecord $member
 */
class MemberRoleActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_member_role}}';
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

    public function getRole(): ActiveQuery
    {
        return $this->hasOne(RoleActiveRecord::class, ['id' => 'role_id']);
    }
}
