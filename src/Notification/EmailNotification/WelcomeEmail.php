<?php
namespace App\Notification\EmailNotification;

use App\Entity\User;
use App\Notification\EmailBuilder;

class WelcomeEmail extends EmailBuilder
{
    public function send(User $user)
    {
        $this->sendEmail(
            $this->createEmail()
            ->from('hello@example.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Bienvenue dans la communauté Lebongroin, '. $user->getUsername() .' !')
            ->html($this->twig->render('notification/email/welcomeEmail.html.twig', [
                'user' => $user
            ]))
        );
    }
}