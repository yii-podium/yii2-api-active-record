<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Role Active Record.
 *
 * @property int    $id
 * @property string $name
 * @property int    $created_at
 * @property int    $updated_at
 */
class RoleActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_role}}';
    }

    public function behaviors(): array
    {
        return ['timestamp' => TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 191],
            [['name'], 'unique'],
        ];
    }
}
