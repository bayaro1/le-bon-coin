<?php
namespace App\Notification\EmailNotification;

use App\Notification\EmailBuilder;

class PasswordInitEmail extends EmailBuilder
{
    public function send($user):void 
    {
        $link = self::APP . $this->urlGenerator->generate('security_verifyPasswordInit', [
            'user' => $user->getId(),
            'token' => $user->getPasswordInitToken()
        ]);

        $this->sendEmail(
            $this->createEmail()
            ->from(self::NOREPLY_EMAIL)
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Réinitialisation de votre mot de passe Lebongroin')
            ->html($this->twig->render('notification/email/passwordInitEmail.html.twig', [
                'user' => $user,
                'link' => $link
            ]))
        );
    }
}