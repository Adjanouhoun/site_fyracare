<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260821152453 extends AbstractMigration
{
    public function getDescription(): string { return 'Catalogue de prestations et comptes administrateurs FyraCare'; }

    public function up(Schema $schema): void
    {
        $admin = $schema->createTable('admin_user');
        $admin->addColumn('id', 'integer', ['autoincrement' => true]);
        $admin->addColumn('email', 'string', ['length' => 180]);
        $admin->addColumn('roles', 'json');
        $admin->addColumn('password', 'string', ['length' => 255]);
        $admin->setPrimaryKey(['id']);
        $admin->addUniqueIndex(['email']);

        $service = $schema->createTable('care_service');
        $service->addColumn('id', 'integer', ['autoincrement' => true]);
        foreach (['code' => 80, 'title_fr' => 180, 'title_en' => 180, 'title_ar' => 180] as $name => $length) {
            $service->addColumn($name, 'string', ['length' => $length]);
        }
        foreach (['description_fr', 'description_en', 'description_ar'] as $name) {
            $service->addColumn($name, 'text');
        }
        $service->addColumn('display_order', 'integer');
        $service->addColumn('active', 'boolean');
        $service->addColumn('featured', 'boolean');
        $service->setPrimaryKey(['id']);
        $service->addUniqueIndex(['code']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('care_service');
        $schema->dropTable('admin_user');
    }
}
