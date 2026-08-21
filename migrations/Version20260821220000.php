<?php

declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260821220000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajoute les avis clients modérés'; }
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('testimonial');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('author', 'string', ['length' => 80]);
        $table->addColumn('care', 'string', ['length' => 150]);
        $table->addColumn('content', 'text');
        $table->addColumn('rating', 'integer');
        $table->addColumn('status', 'string', ['length' => 20]);
        $table->addColumn('created_at', 'datetime_immutable');
        $table->setPrimaryKey(['id']);
    }
    public function down(Schema $schema): void { $schema->dropTable('testimonial'); }
}
