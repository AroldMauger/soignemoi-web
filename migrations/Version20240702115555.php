<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702115555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE opinions (id INT AUTO_INCREMENT NOT NULL, doctor_id INT NOT NULL, stay_id INT NOT NULL, date DATETIME NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_BEAF78D087F4FB17 (doctor_id), INDEX IDX_BEAF78D0FB3AF7D6 (stay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE opinions ADD CONSTRAINT FK_BEAF78D087F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
        $this->addSql('ALTER TABLE opinions ADD CONSTRAINT FK_BEAF78D0FB3AF7D6 FOREIGN KEY (stay_id) REFERENCES stays (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE opinions DROP FOREIGN KEY FK_BEAF78D087F4FB17');
        $this->addSql('ALTER TABLE opinions DROP FOREIGN KEY FK_BEAF78D0FB3AF7D6');
        $this->addSql('DROP TABLE opinions');
    }
}
