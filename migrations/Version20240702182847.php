<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702182847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reasons (id INT AUTO_INCREMENT NOT NULL, speciality_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, INDEX IDX_12833BA83B5A08D7 (speciality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reasons ADD CONSTRAINT FK_12833BA83B5A08D7 FOREIGN KEY (speciality_id) REFERENCES specialities (id)');
        $this->addSql('ALTER TABLE stays ADD speciality_id INT NOT NULL, ADD reason_id INT NOT NULL, DROP speciality, DROP reason');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E73B5A08D7 FOREIGN KEY (speciality_id) REFERENCES specialities (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E759BB1592 FOREIGN KEY (reason_id) REFERENCES reasons (id)');
        $this->addSql('CREATE INDEX IDX_E2E919E73B5A08D7 ON stays (speciality_id)');
        $this->addSql('CREATE INDEX IDX_E2E919E759BB1592 ON stays (reason_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E759BB1592');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E73B5A08D7');
        $this->addSql('ALTER TABLE reasons DROP FOREIGN KEY FK_12833BA83B5A08D7');
        $this->addSql('DROP TABLE reasons');
        $this->addSql('DROP TABLE specialities');
        $this->addSql('DROP INDEX IDX_E2E919E73B5A08D7 ON stays');
        $this->addSql('DROP INDEX IDX_E2E919E759BB1592 ON stays');
        $this->addSql('ALTER TABLE stays ADD speciality VARCHAR(255) NOT NULL, ADD reason VARCHAR(255) NOT NULL, DROP speciality_id, DROP reason_id');
    }
}
