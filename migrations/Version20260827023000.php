<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260827023000 extends AbstractMigration
{
    public function getDescription(): string { return 'Nettoie les references de galerie vers des thematiques supprimees'; }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE gallery_item SET category_id = NULL WHERE category_id IS NOT NULL AND category_id NOT IN (SELECT id FROM gallery_category)');
    }

    public function down(Schema $schema): void
    {
    }
}
