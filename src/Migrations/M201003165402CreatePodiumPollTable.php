<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use Podium\ActiveRecordApi\enums\PollChoice;
use yii\db\Connection;
use yii\db\Migration;

class M201003165402CreatePodiumPollTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_poll}}', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'question' => $this->string(255)->notNull(),
            'revealed' => $this->boolean()->notNull()->defaultValue(true),
            'choice_id' => $this->string(45)->notNull()->defaultValue(PollChoice::SINGLE),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'expires_at' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-podium_poll-post_id',
            '{{%podium_poll}}',
            'post_id',
            '{{%podium_post}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_poll}}');

        return true;
    }
}
