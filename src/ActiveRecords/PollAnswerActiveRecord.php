<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Poll Answer Active Record.
 *
 * @property int              $id
 * @property int              $poll_id
 * @property string           $answer
 * @property int              $created_at
 * @property int              $updated_at
 * @property PollActiveRecord $poll
 */
class PollAnswerActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_poll_answer}}';
    }

    public function behaviors(): array
    {
        return ['timestamp' => TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['answer'], 'required'],
            [['answer'], 'string', 'max' => 255],
        ];
    }

    public function getPoll(): ActiveQuery
    {
        return $this->hasOne(PollActiveRecord::class, ['id' => 'poll_id']);
    }
}
