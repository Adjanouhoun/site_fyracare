<?php

namespace App\Tests\Content;

use App\Entity\AdminUser;
use App\Entity\GalleryItem;
use App\Entity\SiteContent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContentManagementTest extends WebTestCase
{
    public function testAdminContentOverrideAndGalleryAreDisplayed(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $tool = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);

        $content = (new SiteContent())->setCode('about_page.title')->setLabel('Titre À propos')->setPage('about_page')->setContentFr('Un titre administré')->setContentEn('Managed title')->setContentAr('عنوان مُدار');
        $media = (new GalleryItem())->setType(GalleryItem::TYPE_VIDEO)->setTitleFr('La vie du centre')->setTitleEn('Life at the centre')->setTitleAr('حياة المركز')->setVideoUrl('https://youtu.be/dQw4w9WgXcQ')->setActive(true)->setFeatured(true);
        $admin = (new AdminUser())->setEmail('cms-test@fyracare.local')->setPassword('unused');
        $em->persist($content); $em->persist($media); $em->persist($admin); $em->flush();

        $client->request('GET', '/fr/a-propos');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Un titre administré');

        $client->request('GET', '/fr/galerie');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'La vie du centre');
        self::assertSelectorExists('iframe[src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"]');

        $client->loginUser($admin);
        $client->request('GET', '/admin/site-content');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Textes & images du site');
        $client->request('GET', '/admin/gallery-item');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Galerie photo / vidéo');
    }
}
