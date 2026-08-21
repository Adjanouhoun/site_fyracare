<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260821160000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajout du prix facultatif des prestations'; }
    public function up(Schema $schema): void { $schema->getTable('care_service')->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'notnull' => false]); }
    public function down(Schema $schema): void { $schema->getTable('care_service')->dropColumn('price'); }
}
