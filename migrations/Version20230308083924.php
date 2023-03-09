<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308083924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, number_plate VARCHAR(50) NOT NULL, number_of_seats INT NOT NULL, model VARCHAR(100) NOT NULL, INDEX IDX_773DE69D44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, postal_code VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE travel (id INT AUTO_INCREMENT NOT NULL, starting_city_id INT NOT NULL, arrival_city_id INT NOT NULL, date_of_departure DATETIME NOT NULL, arrival_date DATETIME NOT NULL, kilometer INT NOT NULL, INDEX IDX_2D0B6BCE3C70C2C2 (starting_city_id), INDEX IDX_2D0B6BCE4067ACA7 (arrival_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_information (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(100) NOT NULL, city VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_information_car (user_information_id INT NOT NULL, car_id INT NOT NULL, INDEX IDX_791C22264575EE58 (user_information_id), INDEX IDX_791C2226C3C6F69F (car_id), PRIMARY KEY(user_information_id, car_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D44F5D008 FOREIGN KEY (brand_id) REFERENCES car_brand (id)');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE3C70C2C2 FOREIGN KEY (starting_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCE4067ACA7 FOREIGN KEY (arrival_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user_information_car ADD CONSTRAINT FK_791C22264575EE58 FOREIGN KEY (user_information_id) REFERENCES user_information (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_information_car ADD CONSTRAINT FK_791C2226C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D44F5D008');
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE3C70C2C2');
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCE4067ACA7');
        $this->addSql('ALTER TABLE user_information_car DROP FOREIGN KEY FK_791C22264575EE58');
        $this->addSql('ALTER TABLE user_information_car DROP FOREIGN KEY FK_791C2226C3C6F69F');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE car_brand');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE travel');
        $this->addSql('DROP TABLE user_information');
        $this->addSql('DROP TABLE user_information_car');
    }
}
