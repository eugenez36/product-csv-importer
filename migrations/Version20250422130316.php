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
                int_product_data_id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                str_product_name VARCHAR(50) NOT NULL,
                str_product_desc VARCHAR(255) NOT NULL,
                str_product_code VARCHAR(10) NOT NULL,
                dtm_added DATETIME DEFAULT NULL,
                dtm_discontinued DATETIME DEFAULT NULL,
                stm_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY(int_product_data_id))
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
