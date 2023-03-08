<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307171018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travel ADD starting_city_id INT NOT NULL, ADD arrival_city_id INT NOT NULL');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE3C70C2C2 FOREIGN KEY (starting_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE4067ACA7 FOREIGN KEY (arrival_city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_2D0B6BCE3C70C2C2 ON travel (starting_city_id)');
        $this->addSql('CREATE INDEX IDX_2D0B6BCE4067ACA7 ON travel (arrival_city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE3C70C2C2');
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE4067ACA7');
        $this->addSql('DROP INDEX IDX_2D0B6BCE3C70C2C2 ON travel');
        $this->addSql('DROP INDEX IDX_2D0B6BCE4067ACA7 ON travel');
        $this->addSql('ALTER TABLE travel DROP starting_city_id, DROP arrival_city_id');
    }
}
