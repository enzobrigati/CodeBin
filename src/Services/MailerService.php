<?php


namespace App\Services;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMessage($subject, $to, $view, $params = []): void
    {
        $message = (new TemplatedEmail())
            ->from(new Address('noreply@deob.fr', 'CodeBin - Ne pas Répondre'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($view)
            ->context($params);
        $this->mailer->send($message);
    }
}