<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Common\Traits\Comm;

/**
 * @author Frogg <admin@frogg.fr>
 */
trait MailerTrait
{
    /** @var \Swift_Mailer $mailer $mailer */
    private $mailer;

    /**
     * @param string $from     mail from
     * @param string $to       mail to
     * @param string $template twig template to display in the mail
     * @param string $subject  mail subject
     * @param object $data     mail extraa data to send to the twig template
     *
     * @see https://symfony.com/doc/current/email/dev_environment.html
     * @see  Controller & injection __construct(\Swift_Mailer $mailer)
     */
    public function send(string $from, string $to, string $template, string $subject, $data) : void
    {

        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->renderView(
                    $template,
                    array('data' => $data)
                ),
                'text/html'
            )/*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;

        $this->mailer->send($message);
    }
}
