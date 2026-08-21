<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260821210000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajoute deux images administrables aux prestations'; }
    public function up(Schema $schema): void
    {
        $table = $schema->getTable('care_service');
        $table->addColumn('image_one', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('image_two', 'string', ['length' => 255, 'notnull' => false]);
    }
    public function down(Schema $schema): void
    {
        $table = $schema->getTable('care_service');
        $table->dropColumn('image_one');
        $table->dropColumn('image_two');
    }
}
