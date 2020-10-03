<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use Podium\ActiveRecordApi\enums\PollChoice;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function time;

/**
 * Poll Active Record.
 *
 * @property int              $id
 * @property int              $post_id
 * @property string           $question
 * @property bool             $revealed
 * @property string           $choice_id
 * @property int              $created_at
 * @property int              $updated_at
 * @property int              $expires_at
 * @property PostActiveRecord $post
 */
class PollActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_poll}}';
    }

    public function behaviors(): array
    {
        return ['timestamp' => TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['revealed'], 'default', 'value' => true],
            [['choice_id'], 'default', 'value' => PollChoice::SINGLE],
            [['question', 'revealed', 'choice_id', 'expires_at', 'answers'], 'required'],
            [['question'], 'string', 'min' => 3],
            [['revealed'], 'boolean'],
            [['choice_id'], 'in', 'range' => PollChoice::keys()],
            [['expires_at'], 'integer', 'min' => time()],
            [['answers'], 'each', 'rule' => ['string', 'min' => 3]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'revealed' => Yii::t('podium.label', 'poll.revealed'),
            'choice_id' => Yii::t('podium.label', 'poll.choice.type'),
            'question' => Yii::t('podium.label', 'poll.question'),
            'expires_at' => Yii::t('podium.label', 'poll.expires'),
            'answers' => Yii::t('podium.label', 'poll.answers'),
        ];
    }

    public function getPost(): ActiveQuery
    {
        return $this->hasOne(PostActiveRecord::class, ['id' => 'post_id']);
    }
}
