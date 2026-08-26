<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260826220000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajoute les contenus administrables et la galerie photo vidéo'; }
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE site_content (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(190) NOT NULL, label VARCHAR(190) NOT NULL, page VARCHAR(80) NOT NULL, type VARCHAR(20) NOT NULL, content_fr CLOB DEFAULT NULL, content_en CLOB DEFAULT NULL, content_ar CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, active BOOLEAN NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uniq_site_content_code ON site_content (code)');
        $this->addSql('CREATE TABLE gallery_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(20) NOT NULL, title_fr VARCHAR(180) NOT NULL, title_en VARCHAR(180) NOT NULL, title_ar VARCHAR(180) NOT NULL, caption_fr CLOB DEFAULT NULL, caption_en CLOB DEFAULT NULL, caption_ar CLOB DEFAULT NULL, media_file VARCHAR(255) DEFAULT NULL, video_url VARCHAR(500) DEFAULT NULL, thumbnail VARCHAR(255) DEFAULT NULL, display_order INTEGER NOT NULL, active BOOLEAN NOT NULL, featured BOOLEAN NOT NULL, created_at DATETIME NOT NULL)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE site_content');
        $this->addSql('DROP TABLE gallery_item');
    }
}
