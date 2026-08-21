<?php
namespace App\Tests\Admin;

use App\Entity\AdminUser;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceAdminTest extends WebTestCase
{
    public function testAuthenticatedAdminCanOpenServiceCatalogue(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $tool = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);

        $admin = (new AdminUser())->setEmail('admin-test@fyracare.local')->setPassword('unused-in-functional-test');
        $service = (new Service())->setCode('massage_test')->setTitleFr('Massage test')->setTitleEn('Test massage')->setTitleAr('تدليك تجريبي')->setDescriptionFr('Description française')->setDescriptionEn('English description')->setDescriptionAr('وصف عربي')->setDisplayOrder(1)->setPrice('1500')->setActive(true)->setFeatured(true);
        $em->persist($admin);
        $em->persist($service);
        $em->flush();

        $client->loginUser($admin);
        $client->request('GET', '/admin/service');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Catalogue des prestations');
        self::assertSelectorTextContains('body', 'Massage test');

        $client->request('GET', '/admin/service/new');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Ajouter une prestation');
        self::assertSelectorTextContains('body', 'Général');
        self::assertSelectorTextContains('body', 'Français');
        self::assertSelectorTextContains('body', 'English');
    }

    public function testAnonymousVisitorIsRedirectedToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/service');
        self::assertResponseRedirects('/admin/connexion');
    }
}
