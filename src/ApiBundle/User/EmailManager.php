<?php

namespace ApiBundle\User;

use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

class EmailManager
{
    private $mailer;
    private $twig;
    private $mailerUser;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, string $mailerUser)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailerUser = $mailerUser;
    }

    /**
     * Send email
     *
     * @param   string   $subject       email subject
     * @param   string   $template      template name
     * @param   string   $recipient     email address from user that will recieve email
     * @param   mixed    $parameters    extra data that template might require (nullable)
     *
     * @return  boolean                 send status
     */
    public function sendEmail(string $subject, string $template, string $recipient, array $parameters = null): bool
    {
        try {
            $message = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($this->mailerUser)
                ->setTo($recipient)
                ->setBody($this->twig->render('ApiBundle:emails:' . $template . '.html.twig', [
                    'parameters' => $parameters
                ]));

            $response = $this->mailer->send($message);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $response;
    }
}
