<?php 
namespace App\Notification;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Twig\Environment;

class EmailBuilder
{
    private Mailer $mailer;

    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $transport = Transport::fromDsn('smtp://800542c71826e3:57cf71441210a7@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login');
        $this->mailer = new Mailer($transport);
    }

    protected function createEmail():Email
    {
        return new Email();
    }

    protected function sendEmail(Email $email)
    {
        $this->mailer->send($email);
    }
}