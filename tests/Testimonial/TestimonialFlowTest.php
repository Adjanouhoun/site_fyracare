<?php

namespace App\Tests\Testimonial;

use App\Entity\Service;
use App\Entity\Testimonial;
use App\Repository\TestimonialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TestimonialFlowTest extends WebTestCase
{
    public function testSubmittedReviewRemainsPendingAndOnlyApprovedReviewsAreShown(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $tool = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
        $service = (new Service())->setCode('soin_test')->setTitleFr('Soin test')->setTitleEn('Test care')->setTitleAr('رعاية تجريبية')->setDescriptionFr('Description française')->setDescriptionEn('English description')->setDescriptionAr('وصف عربي')->setDisplayOrder(1)->setActive(true);
        $approved = (new Testimonial())->setAuthor('Avis publié')->setCare('Soin test')->setContent('Un accompagnement très rassurant et professionnel du début à la fin.')->setRating(5)->setStatus(Testimonial::STATUS_APPROVED);
        $hidden = (new Testimonial())->setAuthor('Avis masqué')->setCare('Soin test')->setContent('Ce commentaire attend encore la validation de l’administration.')->setRating(4);
        $em->persist($service);
        $em->persist($approved);
        $em->persist($hidden);
        $em->flush();

        $crawler = $client->request('GET', '/fr');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Avis publié');
        self::assertSelectorTextNotContains('body', 'Avis masqué');
        $form = $crawler->selectButton('Envoyer mon avis')->form([
            'testimonial[author]' => 'Nouvel avis',
            'testimonial[care]' => 'Soin test',
            'testimonial[rating]' => '5',
            'testimonial[content]' => 'Je souhaite partager une expérience positive suffisamment détaillée.',
        ]);
        $client->submit($form);
        self::assertResponseRedirects('/#testimonials', 303);
        $created = static::getContainer()->get(TestimonialRepository::class)->findOneBy(['author' => 'Nouvel avis']);
        self::assertNotNull($created);
        self::assertSame(Testimonial::STATUS_PENDING, $created->getStatus());
    }
}
