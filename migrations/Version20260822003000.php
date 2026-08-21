<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260822003000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajoute la rubrique multilingue Conseils'; }
    public function up(Schema $schema): void
    {
        $table=$schema->createTable('advice_article');
        $table->addColumn('id','integer',['autoincrement'=>true]);
        $table->addColumn('slug','string',['length'=>180]);
        $table->addColumn('category','string',['length'=>60]);
        foreach (['title_fr','title_en','title_ar'] as $name) $table->addColumn($name,'string',['length'=>180]);
        foreach (['excerpt_fr','excerpt_en','excerpt_ar','content_fr','content_en','content_ar'] as $name) $table->addColumn($name,'text');
        foreach (['seo_title_fr','seo_title_en','seo_title_ar'] as $name) $table->addColumn($name,'string',['length'=>180]);
        foreach (['seo_description_fr','seo_description_en','seo_description_ar'] as $name) $table->addColumn($name,'string',['length'=>320]);
        $table->addColumn('author','string',['length'=>160]);
        $table->addColumn('image','string',['length'=>255,'notnull'=>false]);
        $table->addColumn('published','boolean');
        $table->addColumn('featured','boolean');
        $table->addColumn('published_at','datetime_immutable');
        $table->addColumn('updated_at','datetime_immutable');
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['slug'],'uniq_advice_slug');
        $table->addIndex(['published','published_at'],'idx_advice_publication');
    }
    public function down(Schema $schema): void { $schema->dropTable('advice_article'); }
}
