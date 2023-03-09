<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308084530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_information_travel (user_information_id INT NOT NULL, travel_id INT NOT NULL, INDEX IDX_7FF78254575EE58 (user_information_id), INDEX IDX_7FF7825ECAB15B3 (travel_id), PRIMARY KEY(user_information_id, travel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_information_travel ADD CONSTRAINT FK_7FF78254575EE58 FOREIGN KEY (user_information_id) REFERENCES user_information (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_information_travel ADD CONSTRAINT FK_7FF7825ECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_information_travel DROP FOREIGN KEY FK_7FF78254575EE58');
        $this->addSql('ALTER TABLE user_information_travel DROP FOREIGN KEY FK_7FF7825ECAB15B3');
        $this->addSql('DROP TABLE user_information_travel');
    }
}
