<?php
namespace App\Notification\EmailNotification;

use App\Notification\EmailBuilder;

class Auth2FAEmail extends EmailBuilder
{
    public function send($user):void 
    {
        $token = $user->getToken2FA();

        $this->sendEmail(
            $this->createEmail()
            ->from(self::NOREPLY_EMAIL)
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('code 2FA')
            ->html($this->twig->render('notification/email/auth2FAEmail.html.twig', [
                'user' => $user,
                'token' => $token
            ]))
        );
    }

}