<?php

use Podium\ActiveRecordApi\Enums\MemberStatus;

return [
    'member1' => [
        'id' => 1,
        'user_id' => '1',
        'username' => 'Member1',
        'slug' => 'Member1',
        'status_id' => MemberStatus::ACTIVE,
        'created_at' => 1,
        'updated_at' => 1,
    ],
    'member2' => [
        'id' => 2,
        'user_id' => '2',
        'username' => 'Member2',
        'slug' => 'Member2',
        'status_id' => MemberStatus::BANNED,
        'created_at' => 1,
        'updated_at' => 1,
    ],
    'member3' => [
        'id' => 3,
        'user_id' => '3',
        'username' => 'Member3',
        'slug' => 'Member3',
        'status_id' => MemberStatus::ACTIVE,
        'created_at' => 1,
        'updated_at' => 1,
    ],
    'member4' => [
        'id' => 4,
        'user_id' => '4',
        'username' => 'Member4',
        'slug' => 'Member4',
        'status_id' => MemberStatus::ACTIVE,
        'created_at' => 1,
        'updated_at' => 1,
    ],
];
