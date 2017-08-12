<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 * Table User
 */
class Version20170605091300 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE `User` (
            `userId` INT NOT NULL AUTO_INCREMENT,
            `userFirstname` VARCHAR(100) NULL,
            `userLastname` VARCHAR(100) NULL,
            `userEmail` VARCHAR(100) NULL,
            `userPassword` VARCHAR(100) NULL,
            `userVerificationToken` VARCHAR(255) NULL,
            `userVerified` DATETIME NULL,
            `userPasswordToken` VARCHAR(255) NULL,
            `created` DATETIME NULL,
            `updated` DATETIME NULL,
            `deleted` DATETIME NULL,
            PRIMARY KEY (`userId`));
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
