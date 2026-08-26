<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260826233000 extends AbstractMigration
{
    public function getDescription(): string { return 'Classe les contenus par page et ajoute les thématiques de galerie'; }
    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE site_content ADD COLUMN site_page VARCHAR(80) DEFAULT 'general' NOT NULL");
        $this->addSql('CREATE TABLE gallery_category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, slug VARCHAR(120) NOT NULL, title_fr VARCHAR(180) NOT NULL, title_en VARCHAR(180) NOT NULL, title_ar VARCHAR(180) NOT NULL, description_fr CLOB DEFAULT NULL, description_en CLOB DEFAULT NULL, description_ar CLOB DEFAULT NULL, display_order INTEGER NOT NULL, active BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_GALLERY_CATEGORY_SLUG ON gallery_category (slug)');
        $this->addSql('ALTER TABLE gallery_item ADD COLUMN category_id INTEGER DEFAULT NULL REFERENCES gallery_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_GALLERY_ITEM_CATEGORY ON gallery_item (category_id)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_GALLERY_ITEM_CATEGORY');
        $this->addSql('ALTER TABLE gallery_item DROP COLUMN category_id');
        $this->addSql('DROP TABLE gallery_category');
        $this->addSql('ALTER TABLE site_content DROP COLUMN site_page');
    }
}
