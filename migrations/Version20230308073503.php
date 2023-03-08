<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308073503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE travel (id INT AUTO_INCREMENT NOT NULL, starting_city_id INT NOT NULL, arrival_city_id INT NOT NULL, date_of_departure DATETIME NOT NULL, arrival_date DATETIME NOT NULL, kilometer INT NOT NULL, INDEX IDX_2D0B6BCE3C70C2C2 (starting_city_id), INDEX IDX_2D0B6BCE4067ACA7 (arrival_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE3C70C2C2 FOREIGN KEY (starting_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE4067ACA7 FOREIGN KEY (arrival_city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE3C70C2C2');
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE4067ACA7');
        $this->addSql('DROP TABLE travel');
    }
}
