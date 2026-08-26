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

        $client->request('GET', '/fr');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('.gallery-section');

        $client->request('GET', '/fr/galerie');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'La vie du centre');
        self::assertSelectorTextContains('.gallery-theme-heading h2', 'La vie au centre');
        self::assertSelectorExists('.gallery-theme-overview');
        self::assertSelectorTextContains('.gallery-theme-card-copy strong', 'La vie au centre');
        self::assertSelectorExists('.gallery-theme-cover');
        self::assertSelectorExists('iframe[src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"]');

        $client->loginUser($admin);
        $client->request('GET', '/admin/pages/home');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.section-card:nth-child(1)', 'Bandeau supérieur');
        self::assertSelectorTextContains('.section-card:nth-child(2)', 'Bienvenue à FyraCare');
        self::assertSelectorTextContains('.section-card:nth-child(3)', 'Votre parcours FyraCare');
        self::assertSelectorNotExists('.section-card[href*="section=testimonials"]');
        self::assertSelectorNotExists('.section-card[href*="section=testimonial_form"]');
        self::assertSelectorNotExists('.section-card[href*="section=booking"]');
        self::assertSelectorNotExists('.section-card[href*="section=gallery"]');
        $client->request('GET', '/admin/pages/about');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'À propos');
        self::assertSelectorTextContains('.section-card', 'Bannière');
        $crawler = $client->request('GET', '/admin/pages/about?section=hero');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('textarea[name="contents['.$content->getId().'][fr]"]');
        self::assertSelectorNotExists('a.action-new');
        $token = $crawler->filter('input[name="_token"]')->attr('value');
        $client->request('POST', '/admin/pages/about?section=hero', [
            '_token'=>$token,
            'contents'=>[(string) $content->getId()=>['fr'=>'Titre modifié par section','en'=>'Section edited title','ar'=>'عنوان معدل']],
        ]);
        self::assertResponseRedirects('/admin/pages/about?section=hero');
        $updatedContent = static::getContainer()->get(EntityManagerInterface::class)->getRepository(SiteContent::class)->find($content->getId());
        self::assertSame('Titre modifié par section', $updatedContent->getContentFr());

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
        self::assertSelectorTextContains('body', 'Média principal de la thématique');
    }
}
