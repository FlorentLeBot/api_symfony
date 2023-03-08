<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308073233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_information (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(100) NOT NULL, city VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE3C70C2C2 FOREIGN KEY (starting_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE4067ACA7 FOREIGN KEY (arrival_city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_2D0B6BCE3C70C2C2 ON travel (starting_city_id)');
        $this->addSql('CREATE INDEX IDX_2D0B6BCE4067ACA7 ON travel (arrival_city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_information');
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE3C70C2C2');
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE4067ACA7');
        $this->addSql('DROP INDEX IDX_2D0B6BCE3C70C2C2 ON travel');
        $this->addSql('DROP INDEX IDX_2D0B6BCE4067ACA7 ON travel');
    }
}
