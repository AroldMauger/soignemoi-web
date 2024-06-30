<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240630084458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF632B07E31');
        $this->addSql('DROP INDEX IDX_D499BFF632B07E31 ON planning');
        $this->addSql('ALTER TABLE planning CHANGE doctor_id_id doctor_id INT NOT NULL');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF687F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
        $this->addSql('CREATE INDEX IDX_D499BFF687F4FB17 ON planning (doctor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF687F4FB17');
        $this->addSql('DROP INDEX IDX_D499BFF687F4FB17 ON planning');
        $this->addSql('ALTER TABLE planning CHANGE doctor_id doctor_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF632B07E31 FOREIGN KEY (doctor_id_id) REFERENCES doctors (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D499BFF632B07E31 ON planning (doctor_id_id)');
    }
}
