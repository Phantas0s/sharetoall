<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 * Table Message
 */
class Version20170606054940 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE `Message` (
            `messageId` INT NOT NULL AUTO_INCREMENT,
            `messageContent` TEXT NULL,
            `created` DATETIME NULL,
            `updated` DATETIME NULL,
            `deleted` DATETIME NULL,
            PRIMARY KEY (`messageId`));
        ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
