<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use yii\test\ActiveFixture;

class MemberFixture extends ActiveFixture
{
    public $modelClass = MemberActiveRecord::class;
}
