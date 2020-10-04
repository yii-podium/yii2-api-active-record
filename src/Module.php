<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi;

use Podium\ActiveRecordApi\repositories\AcquaintanceRepository;
use Podium\ActiveRecordApi\repositories\BookmarkRepository;
use Podium\ActiveRecordApi\repositories\CategoryRepository;
use Podium\ActiveRecordApi\repositories\ForumRepository;
use Podium\ActiveRecordApi\repositories\GroupRepository;
use Podium\ActiveRecordApi\repositories\MemberRepository;
use Podium\ActiveRecordApi\repositories\MessageRepository;
use Podium\ActiveRecordApi\repositories\PostRepository;
use Podium\ActiveRecordApi\repositories\RankRepository;
use Podium\ActiveRecordApi\repositories\SubscriptionRepository;
use Podium\ActiveRecordApi\repositories\ThreadRepository;
use Podium\ActiveRecordApi\repositories\ThumbRepository;
use Podium\Api\Module as BasePodium;
use Yii;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;

/**
 * Podium API with Active Records
 * Yii 2 Forum Engine.
 *
 * @author Paweł Bizley Brzozowski <pawel@positive.codes>
 *
 * @version 1.0.0
 *
 * @license Apache License 2.0
 *
 * https://github.com/yii-podium/yii2-api-active-record
 * Please report all issues at GitHub
 * https://github.com/yii-podium/yii2-api-active-record/issues
 *
 * Podium requires Yii 2
 * http://www.yiiframework.com
 * https://github.com/yiisoft/yii2
 */
class Module extends BasePodium
{
    private string $version = '0.1.0';

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Returns the configuration of core Podium components.
     */
    public function coreComponents(): array
    {
        $memberRepository = MemberRepository::class;

        return ArrayHelper::merge(
            parent::coreComponents(),
            [
                'account' => ['repositoryConfig' => $memberRepository],
                'category' => ['repositoryConfig' => CategoryRepository::class],
                'forum' => ['repositoryConfig' => ForumRepository::class],
                'group' => ['repositoryConfig' => GroupRepository::class],
                'logger' => ['repositoryConfig' => LogRepository::class],
                'member' => [
                    'repositoryConfig' => $memberRepository,
                    'acquaintanceRepositoryConfig' => AcquaintanceRepository::class,
                ],
                'message' => ['repositoryConfig' => MessageRepository::class],
                'post' => [
                    'postRepositoryConfig' => PostRepository::class,
                    'thumbRepositoryConfig' => ThumbRepository::class,
                ],
                'rank' => ['repositoryConfig' => RankRepository::class],
                'thread' => [
                    'threadRepositoryConfig' => ThreadRepository::class,
                    'bookmarkRepositoryConfig' => BookmarkRepository::class,
                    'subscriptionRepositoryConfig' => SubscriptionRepository::class,
                ],
            ]
        );
    }

    protected function prepareTranslations(): void
    {
        Yii::$app->getI18n()->translations['podium.enum'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'forceTranslation' => true,
            'basePath' => __DIR__.'/Messages',
        ];
        Yii::$app->getI18n()->translations['podium.label'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'forceTranslation' => true,
            'basePath' => __DIR__.'/Messages',
        ];

        parent::prepareTranslations();
    }
}
