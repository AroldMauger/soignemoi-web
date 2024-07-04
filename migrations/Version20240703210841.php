<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240703210841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE doctors (id INT AUTO_INCREMENT NOT NULL, speciality_id INT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, identification VARCHAR(255) NOT NULL, INDEX IDX_B67687BE3B5A08D7 (speciality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicines (id INT AUTO_INCREMENT NOT NULL, prescrition_id INT NOT NULL, name VARCHAR(255) NOT NULL, dosage VARCHAR(255) NOT NULL, startdate DATE NOT NULL, enddate DATE NOT NULL, INDEX IDX_885F06BC732B1533 (prescrition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE opinions (id INT AUTO_INCREMENT NOT NULL, doctor_id INT NOT NULL, stay_id INT NOT NULL, date DATETIME NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_BEAF78D087F4FB17 (doctor_id), INDEX IDX_BEAF78D0FB3AF7D6 (stay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, doctor_id INT NOT NULL, date DATE NOT NULL, INDEX IDX_D499BFF687F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prescriptions (id INT AUTO_INCREMENT NOT NULL, stay_id INT NOT NULL, INDEX IDX_E41E1AC3FB3AF7D6 (stay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reasons (id INT AUTO_INCREMENT NOT NULL, speciality_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, INDEX IDX_12833BA83B5A08D7 (speciality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slot (id INT AUTO_INCREMENT NOT NULL, planning_id INT DEFAULT NULL, doctor_id INT NOT NULL, starttime TIME NOT NULL, endtime TIME NOT NULL, isbooked TINYINT(1) NOT NULL, INDEX IDX_AC0E20673D865311 (planning_id), INDEX IDX_AC0E206787F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stays (id INT AUTO_INCREMENT NOT NULL, speciality_id INT NOT NULL, reason_id INT NOT NULL, doctor_id INT NOT NULL, slot_id INT NOT NULL, user_id INT NOT NULL, entrydate DATETIME NOT NULL, leavingdate DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_E2E919E73B5A08D7 (speciality_id), INDEX IDX_E2E919E759BB1592 (reason_id), INDEX IDX_E2E919E787F4FB17 (doctor_id), INDEX IDX_E2E919E759E5119C (slot_id), INDEX IDX_E2E919E7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doctors ADD CONSTRAINT FK_B67687BE3B5A08D7 FOREIGN KEY (speciality_id) REFERENCES specialities (id)');
        $this->addSql('ALTER TABLE medicines ADD CONSTRAINT FK_885F06BC732B1533 FOREIGN KEY (prescrition_id) REFERENCES prescriptions (id)');
        $this->addSql('ALTER TABLE opinions ADD CONSTRAINT FK_BEAF78D087F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
        $this->addSql('ALTER TABLE opinions ADD CONSTRAINT FK_BEAF78D0FB3AF7D6 FOREIGN KEY (stay_id) REFERENCES stays (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF687F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
        $this->addSql('ALTER TABLE prescriptions ADD CONSTRAINT FK_E41E1AC3FB3AF7D6 FOREIGN KEY (stay_id) REFERENCES stays (id)');
        $this->addSql('ALTER TABLE reasons ADD CONSTRAINT FK_12833BA83B5A08D7 FOREIGN KEY (speciality_id) REFERENCES specialities (id)');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E20673D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E206787F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E73B5A08D7 FOREIGN KEY (speciality_id) REFERENCES specialities (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E759BB1592 FOREIGN KEY (reason_id) REFERENCES reasons (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E787F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E759E5119C FOREIGN KEY (slot_id) REFERENCES slot (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctors DROP FOREIGN KEY FK_B67687BE3B5A08D7');
        $this->addSql('ALTER TABLE medicines DROP FOREIGN KEY FK_885F06BC732B1533');
        $this->addSql('ALTER TABLE opinions DROP FOREIGN KEY FK_BEAF78D087F4FB17');
        $this->addSql('ALTER TABLE opinions DROP FOREIGN KEY FK_BEAF78D0FB3AF7D6');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF687F4FB17');
        $this->addSql('ALTER TABLE prescriptions DROP FOREIGN KEY FK_E41E1AC3FB3AF7D6');
        $this->addSql('ALTER TABLE reasons DROP FOREIGN KEY FK_12833BA83B5A08D7');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E20673D865311');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E206787F4FB17');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E73B5A08D7');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E759BB1592');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E787F4FB17');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E759E5119C');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E7A76ED395');
        $this->addSql('DROP TABLE doctors');
        $this->addSql('DROP TABLE medicines');
        $this->addSql('DROP TABLE opinions');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE prescriptions');
        $this->addSql('DROP TABLE reasons');
        $this->addSql('DROP TABLE slot');
        $this->addSql('DROP TABLE specialities');
        $this->addSql('DROP TABLE stays');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
