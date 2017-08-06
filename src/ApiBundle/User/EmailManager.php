<?php

namespace ApiBundle\User;

class EmailManager
{
    private $mailer;
    private $twig;
    private $mailerUser;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $mailerUser)
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
    public function sendEmail($subject, $template, $recipient, $parameters = null)
    {
        try {
            $message = \Swift_Message::newInstance()
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
