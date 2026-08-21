<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260821230000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajoute les réservations, disponibilités et messages de contact'; }
    public function up(Schema $schema): void
    {
        $availability = $schema->createTable('availability');
        $availability->addColumn('id', 'integer', ['autoincrement' => true]);
        $availability->addColumn('starts_at', 'datetime_immutable');
        $availability->addColumn('active', 'boolean');
        $availability->setPrimaryKey(['id']);

        $appointment = $schema->createTable('appointment');
        $appointment->addColumn('id', 'integer', ['autoincrement' => true]);
        $appointment->addColumn('service_id', 'integer');
        $appointment->addColumn('availability_id', 'integer');
        $appointment->addColumn('full_name', 'string', ['length' => 120]);
        $appointment->addColumn('phone', 'string', ['length' => 30]);
        $appointment->addColumn('email', 'string', ['length' => 180, 'notnull' => false]);
        $appointment->addColumn('note', 'text', ['notnull' => false]);
        $appointment->addColumn('status', 'string', ['length' => 20]);
        $appointment->addColumn('created_at', 'datetime_immutable');
        $appointment->setPrimaryKey(['id']);
        $appointment->addIndex(['service_id']);
        $appointment->addIndex(['availability_id']);
        $appointment->addForeignKeyConstraint('care_service', ['service_id'], ['id']);
        $appointment->addForeignKeyConstraint('availability', ['availability_id'], ['id']);

        $contact = $schema->createTable('contact_message');
        $contact->addColumn('id', 'integer', ['autoincrement' => true]);
        $contact->addColumn('name', 'string', ['length' => 120]);
        $contact->addColumn('email', 'string', ['length' => 180]);
        $contact->addColumn('phone', 'string', ['length' => 30, 'notnull' => false]);
        $contact->addColumn('subject', 'string', ['length' => 180]);
        $contact->addColumn('message', 'text');
        $contact->addColumn('status', 'string', ['length' => 20]);
        $contact->addColumn('created_at', 'datetime_immutable');
        $contact->setPrimaryKey(['id']);
    }
    public function down(Schema $schema): void
    {
        $schema->dropTable('appointment');
        $schema->dropTable('availability');
        $schema->dropTable('contact_message');
    }
}
