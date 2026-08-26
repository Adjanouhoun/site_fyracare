<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260827013000 extends AbstractMigration
{
    public function getDescription(): string { return 'Corrige le libelle et le slug de la categorie nouveaux arrivages'; }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE gallery_category SET slug = 'nouveaux-arrivages', title_fr = 'Nouveaux arrivages', title_en = 'New arrivals', title_ar = 'إضافات جديدة' WHERE slug IN ('nouveau arrivage', 'noueau arrivage')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE gallery_category SET slug = 'noueau arrivage', title_fr = 'noueau arrivage' WHERE slug = 'nouveaux-arrivages'");
    }
}
