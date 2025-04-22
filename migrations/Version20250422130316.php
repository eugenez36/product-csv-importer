<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422130316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE product (
                intProductDataId INT UNSIGNED AUTO_INCREMENT NOT NULL,
                strProductName VARCHAR(50) NOT NULL,
                strProductDesc VARCHAR(255) NOT NULL,
                strProductCode VARCHAR(10) NOT NULL,
                dtmAdded DATETIME DEFAULT NULL,
                dtmDiscontinued DATETIME DEFAULT NULL,
                stmTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY(intProductDataId))
                DEFAULT CHARACTER SET latin1 ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
    }
}
