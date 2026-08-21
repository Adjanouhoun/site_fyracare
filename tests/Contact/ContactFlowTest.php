<?php
namespace App\Tests\Contact;
use App\Repository\ContactMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
final class ContactFlowTest extends WebTestCase {
 public function testContactMessageIsStored():void{$client=static::createClient();$em=static::getContainer()->get(EntityManagerInterface::class);$tool=new SchemaTool($em);$metadata=$em->getMetadataFactory()->getAllMetadata();$tool->dropSchema($metadata);$tool->createSchema($metadata);$crawler=$client->request('GET','/fr/contact');self::assertResponseIsSuccessful();$form=$crawler->selectButton('Envoyer le message')->form(['contact_message[name]'=>'Aminata Test','contact_message[email]'=>'aminata@example.com','contact_message[phone]'=>'+222 44 00 00 00','contact_message[subject]'=>'Question sur une prestation','contact_message[message]'=>'Je souhaite recevoir davantage de renseignements sur cette prestation.']);$client->submit($form);self::assertResponseRedirects('/fr/contact',303);self::assertNotNull(static::getContainer()->get(ContactMessageRepository::class)->findOneBy(['email'=>'aminata@example.com']));}
}
