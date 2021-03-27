<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\ActiveRecords;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Forum Active Record.
 *
 * @property int                  $id
 * @property int                  $author_id
 * @property int                  $category_id
 * @property string               $name
 * @property string               $slug
 * @property string               $description
 * @property bool                 $visible
 * @property int                  $sort
 * @property int                  $threads_count
 * @property int                  $posts_count
 * @property int                  $created_at
 * @property int                  $updated_at
 * @property bool                 $archived
 * @property CategoryActiveRecord $category
 * @property MemberActiveRecord   $author
 * @property GroupActiveRecord[]  $groups
 */
class ForumActiveRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%podium_forum}}';
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
            [['description'], 'string', 'max' => 255],
            [['sort'], 'integer'],
            [['slug'], 'match', 'pattern' => '/^[a-zA-Z0-9\-]{0,191}$/'],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('podium.label', 'forum.name'),
            'description' => Yii::t('podium.label', 'forum.description'),
            'sort' => Yii::t('podium.label', 'forum.sort'),
            'slug' => Yii::t('podium.label', 'forum.slug'),
        ];
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(CategoryActiveRecord::class, ['id' => 'category_id']);
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(MemberActiveRecord::class, ['id' => 'author_id']);
    }

    public function getForumGroups(): ActiveQuery
    {
        return $this->hasMany(ForumGroupActiveRecord::class, ['forum_id' => 'id']);
    }

    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(GroupActiveRecord::class, ['id' => 'group_id'])->via('forumGroups');
    }
}
