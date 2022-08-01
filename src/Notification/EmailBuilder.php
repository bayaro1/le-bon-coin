<?php 
namespace App\Notification;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailBuilder
{
    private Mailer $mailer;

    protected Environment $twig;

    protected UrlGeneratorInterface $urlGenerator;

    protected const APP = 'http://localhost:8000';

    protected const CONTACT_EMAIL = 'contact@lebongroin.fr';
    
    protected const NOREPLY_EMAIL = 'noreply@lebongroin.fr';

    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
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