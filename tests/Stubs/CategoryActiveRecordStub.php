<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;

class CategoryActiveRecordStub extends CategoryActiveRecord
{
    use ActiveRecordStubTrait;

    public function attributes(): array
    {
        return ['id', 'author_id', 'name', 'slug', 'description', 'visible', 'sort', 'archived', 'created_at', 'updated_at', 'author'];
    }
}
