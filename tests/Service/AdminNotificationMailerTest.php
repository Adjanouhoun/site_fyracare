<?php
namespace App\Tests\Service;
use App\Entity\Appointment;
use App\Entity\Availability;
use App\Entity\Service;
use App\Entity\Testimonial;
use App\Service\AdminNotificationMailer;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
final class AdminNotificationMailerTest extends TestCase {
 public function testReviewNotificationIsAddressedToFyraCare():void{$sent=null;$mailer=$this->createMock(MailerInterface::class);$mailer->expects(self::once())->method('send')->willReturnCallback(function(Email $email)use(&$sent):void{$sent=$email;});$testimonial=(new Testimonial())->setAuthor('Mariam A.')->setCare('Massage prénatal')->setRating(5)->setContent('Un accompagnement très rassurant et professionnel.');(new AdminNotificationMailer($mailer,new NullLogger(),'site@fyracare.org'))->newTestimonial($testimonial);self::assertInstanceOf(Email::class,$sent);self::assertSame('site@fyracare.org',$sent->getTo()[0]->getAddress());self::assertSame('Nouvel avis à valider — FyraCare',$sent->getSubject());}
 public function testAppointmentNotificationContainsBookingDetails():void{$sent=null;$mailer=$this->createMock(MailerInterface::class);$mailer->expects(self::once())->method('send')->willReturnCallback(function(Email $email)use(&$sent):void{$sent=$email;});$service=(new Service())->setTitleFr('Préparation à la naissance');$slot=(new Availability())->setStartsAt(new \DateTimeImmutable('2026-09-02 15:00'));$appointment=(new Appointment())->setService($service)->setAvailability($slot)->setFullName('Aïcha Test')->setPhone('+222 44 12 34 56')->setEmail('aicha@example.com')->setNote('Premier rendez-vous');(new AdminNotificationMailer($mailer,new NullLogger(),'site@fyracare.org'))->newAppointment($appointment);self::assertInstanceOf(Email::class,$sent);self::assertStringContainsString('Aïcha Test',$sent->getTextBody());self::assertStringContainsString('02/09/2026 à 15:00',$sent->getTextBody());}
}
