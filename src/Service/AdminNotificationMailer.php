<?php

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Testimonial;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final class AdminNotificationMailer
{
    public function __construct(private MailerInterface $mailer, private LoggerInterface $logger, private string $notificationEmail) {}

    public function newTestimonial(Testimonial $testimonial): void
    {
        $this->send('Nouvel avis à valider — FyraCare', sprintf("Un nouvel avis vient d’être déposé.\n\nCliente : %s\nPrestation : %s\nNote : %d/5\n\nAvis :\n%s\n\nConnectez-vous à l’administration FyraCare pour le valider ou le refuser.", $testimonial->getAuthor(), $testimonial->getCare(), $testimonial->getRating(), $testimonial->getContent()));
    }

    public function newAppointment(Appointment $appointment): void
    {
        $slot = $appointment->getAvailability()?->getStartsAt();
        $this->send('Nouvelle demande de rendez-vous — FyraCare', sprintf("Une nouvelle demande de rendez-vous vient d’être enregistrée.\n\nCliente : %s\nTéléphone : %s\nE-mail : %s\nPrestation : %s\nCréneau : %s\n\nMessage :\n%s\n\nConnectez-vous à l’administration FyraCare pour confirmer la demande.", $appointment->getFullName(), $appointment->getPhone(), $appointment->getEmail() ?: 'Non renseigné', $appointment->getService()?->getTitleFr() ?: 'Non renseignée', $slot?->format('d/m/Y à H:i') ?: 'Non renseigné', $appointment->getNote() ?: 'Aucun message'));
    }

    private function send(string $subject, string $content): void
    {
        $email = (new Email())->from(new Address($this->notificationEmail, 'Site FyraCare'))->to($this->notificationEmail)->subject($subject)->text($content);
        try { $this->mailer->send($email); } catch (TransportExceptionInterface $exception) { $this->logger->error('La notification FyraCare n’a pas pu être envoyée.', ['subject' => $subject, 'exception' => $exception]); }
    }
}
