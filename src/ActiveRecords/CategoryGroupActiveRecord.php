<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Category Group Active Record.
 *
 * @property int                  $category_id
 * @property int                  $group_id
 * @property int                  $created_at
 * @property CategoryActiveRecord $category
 * @property GroupActiveRecord    $group
 */
class CategoryGroupActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_category_group}}';
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

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(CategoryActiveRecord::class, ['id' => 'category_id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(GroupActiveRecord::class, ['id' => 'group_id']);
    }
}
