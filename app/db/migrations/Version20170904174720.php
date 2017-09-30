<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170904174720 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE `sharetoall`.`Network` (
            `networkSlug` VARCHAR(100) NOT NULL,
            `networkName` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`networkSlug`));
        ");

        $this->addSql("
            CREATE TABLE `sharetoall`.`UserNetwork` (
            `userId` INT(11) NOT NULL,
            `networkSlug` VARCHAR(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
            `userNetworkToken` VARCHAR(255) NOT NULL,
            `created` DATETIME NULL,
            `updated` DATETIME NULL,
            `deleted` DATETIME NULL,
            PRIMARY KEY (`userId`, `networkSlug`),
            INDEX `fk_UserNetwork_2_idx` (`networkSlug` ASC),
            CONSTRAINT `fk_UserNetwork_1`
                FOREIGN KEY (`userId`)
                REFERENCES `sharetoall`.`User` (`userId`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `fk_UserNetwork_2`
                FOREIGN KEY (`networkSlug`)
                REFERENCES `sharetoall`.`Network` (`networkSlug`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);
        ");

        $this->addSql("
            INSERT INTO `sharetoall`.`Network` (`networkSlug`, `networkName`) VALUES ('twitter', 'Twitter');
            INSERT INTO `sharetoall`.`Network` (`networkSlug`, `networkName`) VALUES ('linkedin', 'Linkedin');
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
