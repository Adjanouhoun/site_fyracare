<?php
namespace App\Tests\Booking;
use App\Entity\Appointment;
use App\Entity\Availability;
use App\Entity\Service;
use App\Repository\AppointmentRepository;
use App\Repository\AvailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
final class BookingFlowTest extends WebTestCase {
 public function testBookingIsStoredAndSlotBecomesUnavailable():void{$client=static::createClient();$em=static::getContainer()->get(EntityManagerInterface::class);$tool=new SchemaTool($em);$metadata=$em->getMetadataFactory()->getAllMetadata();$tool->dropSchema($metadata);$tool->createSchema($metadata);$service=(new Service())->setCode('booking_test')->setTitleFr('Soin réservation')->setTitleEn('Booking care')->setTitleAr('خدمة')->setDescriptionFr('Description')->setDescriptionEn('Description')->setDescriptionAr('وصف')->setActive(true);$slot=(new Availability())->setStartsAt(new \DateTimeImmutable('+2 days 10:00'));$em->persist($service);$em->persist($slot);$em->flush();$slotId=$slot->getId();$crawler=$client->request('GET','/fr');self::assertResponseIsSuccessful();$form=$crawler->selectButton('Envoyer la demande')->form(['appointment[service]'=>$service->getId(),'appointment[availability]'=>$slotId,'appointment[fullName]'=>'Cliente Test','appointment[phone]'=>'+222 00 00 00 00','appointment[email]'=>'cliente@example.com','appointment[note]'=>'Première demande']);$client->submit($form);self::assertResponseRedirects('/#reservation',303);$appointment=static::getContainer()->get(AppointmentRepository::class)->findOneBy(['fullName'=>'Cliente Test']);self::assertInstanceOf(Appointment::class,$appointment);self::assertSame(Appointment::STATUS_PENDING,$appointment->getStatus());$savedSlot=static::getContainer()->get(AvailabilityRepository::class)->find($slotId);self::assertNotNull($savedSlot);self::assertFalse($savedSlot->isActive());}
}
