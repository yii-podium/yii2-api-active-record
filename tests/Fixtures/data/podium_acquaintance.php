<?php

use Podium\ActiveRecordApi\Enums\AcquaintanceType;

return [
    'friendship1' => [
        'member_id' => 3,
        'target_id' => 1,
        'type_id' => AcquaintanceType::FRIEND,
        'created_at' => 1,
        'updated_at' => 1,
    ],
    'ignore1' => [
        'member_id' => 1,
        'target_id' => 3,
        'type_id' => AcquaintanceType::IGNORE,
        'created_at' => 1,
        'updated_at' => 1,
    ],
];
