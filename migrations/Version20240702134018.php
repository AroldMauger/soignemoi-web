<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702134018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medicines (id INT AUTO_INCREMENT NOT NULL, prescrition_id INT NOT NULL, name VARCHAR(255) NOT NULL, dosage VARCHAR(255) NOT NULL, startdate DATE NOT NULL, enddate DATE NOT NULL, INDEX IDX_885F06BC732B1533 (prescrition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prescriptions (id INT AUTO_INCREMENT NOT NULL, stay_id INT NOT NULL, INDEX IDX_E41E1AC3FB3AF7D6 (stay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE medicines ADD CONSTRAINT FK_885F06BC732B1533 FOREIGN KEY (prescrition_id) REFERENCES prescriptions (id)');
        $this->addSql('ALTER TABLE prescriptions ADD CONSTRAINT FK_E41E1AC3FB3AF7D6 FOREIGN KEY (stay_id) REFERENCES stays (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicines DROP FOREIGN KEY FK_885F06BC732B1533');
        $this->addSql('ALTER TABLE prescriptions DROP FOREIGN KEY FK_E41E1AC3FB3AF7D6');
        $this->addSql('DROP TABLE medicines');
        $this->addSql('DROP TABLE prescriptions');
    }
}
