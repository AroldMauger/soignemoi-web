<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240629082330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add roles JSON column to users table';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne roles de type JSON avec une valeur par défaut vide
        $this->addSql('ALTER TABLE users ADD roles JSON NOT NULL DEFAULT \'[]\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP roles');
    }
}