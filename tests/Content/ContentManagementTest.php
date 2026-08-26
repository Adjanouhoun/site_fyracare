<?php

namespace App\Tests\Content;

use App\Entity\AdminUser;
use App\Entity\GalleryItem;
use App\Entity\GalleryCategory;
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

        $content = (new SiteContent())->setCode('about_page.title')->setLabel('Titre À propos')->setSitePage('about')->setPage('introduction')->setContentFr('Un titre administré')->setContentEn('Managed title')->setContentAr('عنوان مُدار');
        $contactContent = (new SiteContent())->setCode('contact.title')->setLabel('Titre Contact invisible')->setSitePage('contact')->setPage('introduction')->setContentFr('Contact administré');
        $category = (new GalleryCategory())->setSlug('vie-du-centre')->setTitleFr('La vie au centre')->setTitleEn('Life at the centre')->setTitleAr('حياة المركز')->setDescriptionFr('Les espaces et les moments de soin.')->setActive(true);
        $media = (new GalleryItem())->setCategory($category)->setType(GalleryItem::TYPE_VIDEO)->setTitleFr('La vie du centre')->setTitleEn('Life at the centre')->setTitleAr('حياة المركز')->setVideoUrl('https://youtu.be/dQw4w9WgXcQ')->setActive(true)->setFeatured(true);
        $admin = (new AdminUser())->setEmail('cms-test@fyracare.local')->setPassword('unused');
        $em->persist($content); $em->persist($contactContent); $em->persist($category); $em->persist($media); $em->persist($admin); $em->flush();

        $client->request('GET', '/fr/a-propos');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Un titre administré');

        $client->request('GET', '/fr/galerie');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'La vie du centre');
        self::assertSelectorTextContains('.gallery-theme-heading h2', 'La vie au centre');
        self::assertSelectorExists('iframe[src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"]');

        $client->loginUser($admin);
        $client->request('GET', '/admin/site-content');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Modifier les contenus présents sur le site');
        self::assertSelectorNotExists('a.action-new');
        $client->request('GET', '/admin/site-content?contentPage=about');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Titre À propos');
        self::assertSelectorTextNotContains('body', 'Titre Contact invisible');
        $client->request('GET', '/admin/gallery-category');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Thématiques de galerie');
        $client->request('GET', '/admin/gallery-item');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Galerie photo / vidéo');
    }
}
