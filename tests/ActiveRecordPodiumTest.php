<?php

declare(strict_types=1);

namespace Podium\Tests;

use Podium\ActiveRecordApi\ActiveRecordPodium;

class ActiveRecordPodiumTest extends AppTestCase
{
    public function testVersion(): void
    {
        self::assertEquals('0.1.0', (new ActiveRecordPodium())->getVersion());
    }
}
