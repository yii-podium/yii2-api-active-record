<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

use yii\data\ActiveDataFilter;

class ActiveDataFilterStub extends ActiveDataFilter
{
    public static bool $buildCalled = false;
    /**
     * @var mixed
     */
    public static $buildResult = false;

    public function build($runValidation = true)
    {
        static::$buildCalled = true;
        return static::$buildResult;
    }
}
