<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701140350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stays (id INT AUTO_INCREMENT NOT NULL, doctor_id INT NOT NULL, slot_id INT NOT NULL, user_id INT NOT NULL, entrydate DATETIME NOT NULL, leavingdate DATETIME NOT NULL, speciality VARCHAR(255) NOT NULL, reason VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_E2E919E787F4FB17 (doctor_id), INDEX IDX_E2E919E759E5119C (slot_id), INDEX IDX_E2E919E7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E787F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E759E5119C FOREIGN KEY (slot_id) REFERENCES slot (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E787F4FB17');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E759E5119C');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E7A76ED395');
        $this->addSql('DROP TABLE stays');
    }
}
