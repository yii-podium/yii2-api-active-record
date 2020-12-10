<?php

declare(strict_types=1);

namespace Podium\Tests;

use Podium\ActiveRecordApi\ActiveRecordPodium;
use Yii;
use yii\base\InvalidRouteException;
use yii\console\Application;
use yii\console\Exception as ConsoleException;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Exception as DbException;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;
use yii\test\FixtureTrait;

use function fwrite;
use function ob_end_clean;
use function ob_end_flush;
use function ob_start;

class DbTestCase extends AppTestCase
{
    use FixtureTrait;

    public ?ActiveRecordPodium $podium = null;

    protected static string $driverName = 'mysql';

    protected static array $database = [];

    protected static ?Connection $db = null;

    public static array $params = [];

    public static function getParam(string $name, array $default = []): array
    {
        if ([] === static::$params) {
            static::$params = require __DIR__.'/config.php';
        }

        return static::$params[$name] ?? $default;
    }

    protected function setUp(): void
    {
        /** @var ActiveRecordPodium $podium */
        $podium = Yii::$app->podium;
        $this->podium = $podium;

        $this->loadFixtures();
    }

    protected function tearDown(): void
    {
        $this->unloadFixtures();
    }

    /**
     * @throws InvalidRouteException
     * @throws ConsoleException
     * @throws DbException
     */
    public static function setUpBeforeClass(): void
    {
        static::mockApplication();
        static::runSilentMigration('migrate/up');
    }

    /**
     * @throws DbException
     */
    protected static function mockApplication(array $config = [], string $appClass = Application::class): void
    {
        new $appClass(
            ArrayHelper::merge(
                [
                    'id' => 'PodiumAPITest',
                    'basePath' => __DIR__,
                    'vendorPath' => __DIR__.'/../vendor/',
                    'controllerMap' => [
                        'migrate' => [
                            'class' => EchoMigrateController::class,
                            'migrationNamespaces' => ['Podium\ActiveRecordApi\Migrations'],
                            'migrationPath' => null,
                            'interactive' => false,
                            'compact' => true,
                        ],
                    ],
                    'components' => [
                        'db' => static::getConnection(),
                        'i18n' => [
                            'translations' => [
                                'podium.*' => [
                                    'class' => PhpMessageSource::class,
                                ],
                            ],
                        ],
                        'podium' => ActiveRecordPodium::class,
                    ],
                ],
                $config
            )
        );
    }

    /**
     * @throws InvalidRouteException
     * @throws ConsoleException
     */
    protected static function runSilentMigration(string $route, array $params = []): void
    {
        ob_start();

        if (ExitCode::OK === Yii::$app->runAction($route, $params)) {
            ob_end_clean();
        } else {
            fwrite(STDOUT, "\nMigration failed!\n");
            ob_end_flush();
        }
    }

    /**
     * @throws InvalidRouteException
     * @throws ConsoleException
     */
    public static function tearDownAfterClass(): void
    {
        static::runSilentMigration('migrate/down', ['all']);

        if (static::$db) {
            static::$db->close();
        }

        parent::tearDownAfterClass();
    }

    /**
     * @throws DbException
     */
    public static function getConnection(): Connection
    {
        static::$database = static::getParam(static::$driverName);

        if (null === static::$db) {
            $db = new Connection();

            $db->dsn = static::$database['dsn'];

            if (isset(static::$database['charset'])) {
                $db->charset = static::$database['charset'];
            }

            if (isset(static::$database['username'])) {
                $db->username = static::$database['username'];
                $db->password = static::$database['password'];
            }

            if (isset(static::$database['attributes'])) {
                $db->attributes = static::$database['attributes'];
            }

            if (!$db->isActive) {
                $db->open();
            }

            static::$db = $db;
        }

        return static::$db;
    }
}
