<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Thread Active Record.
 *
 * @property int                 $id
 * @property int                 $author_id
 * @property int                 $forum_id
 * @property string              $name
 * @property string              $slug
 * @property bool                $visible
 * @property bool                $pinned
 * @property bool                $locked
 * @property int                 $posts_count
 * @property int                 $views_count
 * @property int                 $created_post_at
 * @property int                 $updated_post_at
 * @property int                 $created_at
 * @property int                 $updated_at
 * @property bool                $archived
 * @property ForumActiveRecord   $forum
 * @property MemberActiveRecord  $author
 * @property PollActiveRecord    $poll
 * @property GroupActiveRecord[] $groups
 */
class ThreadActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_thread}}';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => TimestampBehavior::class,
            'slug' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'ensureUnique' => true,
                'immutable' => true,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'slug'], 'string', 'max' => 191],
            [['slug'], 'match', 'pattern' => '/^[a-zA-Z0-9\-]{0,191}$/'],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('podium.label', 'thread.name'),
            'slug' => Yii::t('podium.label', 'thread.slug'),
        ];
    }

    public function getForum(): ActiveQuery
    {
        return $this->hasOne(ForumActiveRecord::class, ['id' => 'forum_id']);
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(MemberActiveRecord::class, ['id' => 'author_id']);
    }

    public function getPoll(): ActiveQuery
    {
        return $this->hasOne(PollActiveRecord::class, ['thread_id' => 'id']);
    }

    public function getThreadGroups(): ActiveQuery
    {
        return $this->hasMany(ThreadGroupActiveRecord::class, ['thread_id' => 'id']);
    }

    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(GroupActiveRecord::class, ['id' => 'group_id'])->via('threadGroups');
    }
}
