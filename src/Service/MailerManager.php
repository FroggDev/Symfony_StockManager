<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

/**
 * @author Frogg <admin@frogg.fr>
 */
class MailerManager
{
    /** @var \Swift_Mailer */
    private $mailer;

    /**
     * MailerManager constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @see https://symfony.com/doc/current/email/dev_environment.html
     * @see  Controller & injection __construct(\Swift_Mailer $mailer)
     *
     * @param string $from     mail from
     * @param string $to       mail to
     * @param string $bodyhtml
     * @param string $bodytxt
     * @param string $subject  mail subject
     *
     * @return int
     */
    public function send(string $from, string $to, string $bodyhtml, string $bodytxt, string $subject) : int
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($bodyhtml, 'text/html')
            ->addPart($bodytxt, 'text/plain');

        return $this->mailer->send($message);
    }
}
