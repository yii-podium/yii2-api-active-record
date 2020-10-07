<?php

declare(strict_types=1);

namespace Podium\Tests;

use yii\console\controllers\MigrateController;

class EchoMigrateController extends MigrateController
{
    public function stdout($string): void // BC declaration
    {
        echo $string;
    }
}
