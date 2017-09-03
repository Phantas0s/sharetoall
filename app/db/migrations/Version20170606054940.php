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
            `userId` INT NOT NULL,
            `messageContent` TEXT NULL,
            `created` DATETIME NULL,
            `updated` DATETIME NULL,
            `deleted` DATETIME NULL,
            PRIMARY KEY (`messageId`));
        ");

        $this->addSql("
            ALTER TABLE `Message`
            ADD INDEX `fk_Message_1_idx` (`userId` ASC),
            ADD CONSTRAINT `fk_Message_1`
            FOREIGN KEY (`userId`)
            REFERENCES `User` (`userId`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION;
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
