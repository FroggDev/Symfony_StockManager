<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Command :
 * ---------
 * php bin/console app:product:add [codebar codebar ...]
 *
 * STYLES :
 * ------
https://symfony.com/doc/current/console/style.html
$io = new SymfonyStyle($input, $output);
$io->title('Lorem Ipsum Dolor Sit Amet');
$io->section('Lorem Ipsum Dolor Sit Amet');
$io->note('Lorem Ipsum Dolor Sit Amet');
$io->caution('Lorem Ipsum Dolor Sit Amet');
$io->success('Lorem Ipsum Dolor Sit Amet');
$io->warning('Lorem Ipsum Dolor Sit Amet');
$io->error('Lorem Ipsum Dolor Sit Amet');
$io->table(
    array('Header 1', 'Header 2'),
    array(
        array('Cell 1-1', 'Cell 1-2'),
        array('Cell 2-1', 'Cell 2-2'),
        array('Cell 3-1', 'Cell 3-2'),
    )
);
$io->choice('Select the queue to analyze', array('queue1', 'queue2', 'queue3'), 'queue1');
$io->ask('Select an information');
$io->listing(array(
    'Element #1 Lorem ipsum dolor sit amet',
    'Element #2 Lorem ipsum dolor sit amet',
    'Element #3 Lorem ipsum dolor sit amet',
));
$io->askHidden('What is your password?');

$io->progressStart();
$io->progressStart(100);
$io->progressAdvance();
$io->progressAdvance(10);
$io->progressFinish();
 */

namespace App\Command;

use App\Entity\Product;
use App\Entity\StockProducts;
use App\Entity\User;
use App\Repository\StockProductsRepository;
use App\Repository\UserRepository;
use App\Service\MailerManager;
use App\SiteConfig;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Frogg <admin@frogg.fr>
 *
 *
 * TODO : MANAGE USER LANG FOR THE MAIL
 * TODO : TRANSLATION
 *
 * Symfony console
 * @see https://symfony.com/doc/current/console.html
 */
class AlertExpiresCommand extends Command
{
    /** @const int EXITCODE the normal code returned when exit the command */
    public const EXITCODE = 0;

    /** @var SymfonyStyle */
    private $output;

    /** @var \App\Service\Stock\ProductManager */
    private $productManager;

    /** @var Application */
    private $application;

    /** @var KernelInterface */
    private $kernel;

    /** @var ContainerInterface */
    private $container;

    /** @var array day to alert */
    private $days = [0, 3];

    /** @var StockProductsRepository */
    private $stockProductsRepository;

    /** @var UserRepository */
    private $userRepository;

    private $doctrine;

    private $mailer;

    private $twig;

    /**
     * /!\        DO NOT USE CONTRUCTOR IN COMMANDS      /!\
     * /!\ IT WILL BE CALL ON CONSOLE LOAD WHE CONFIGURE /!\
     */

    /**
     * Set the command name/description/help
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:expires:alert')
            // the short description shown while running "php bin/console list"
            ->setDescription('send altert to user with product that expire soon.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to send alterts to user with product that expire soon.');
    }

    /**
     * Main function
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \App\Exception\Product\ProductTypeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // INIT
        $this->application = $this->getApplication();
        $this->kernel = $this->application->getKernel();
        $this->container = $this->kernel->getContainer();
        $this->twig = $this->container->get('twig');
        $this->doctrine = $this->container->get('doctrine');
        $this->mailer = new MailerManager($this->container->get('mailer'));
        $this->productManager = $this->container->get('app.service.product_manager');
        $this->stockProductsRepository = $this->doctrine->getRepository(StockProducts::class);
        $this->userRepository = $this->doctrine->getRepository(User::class);

        // INIT STYLES
        $this->output = new SymfonyStyle($input, $output);

        // DO MAIN SCRIPT
        $this->alertExpires();
    }


    /**
     * Main script
     *
     * @return int
     */
    private function alertExpires()
    {

        $users = $this->userRepository->findAll();

        /** @var User $user */
        foreach ($users as $user) {

            $messageData = $this->getMessage($user->getStock()->getId());

            if ($messageData['nb'] > 0) {
                $result = $this->sendMail($user, $messageData);

                if(1===$result){
                    $this->output->success("Mail has been sent to ".$user->getEmail());
                }else{
                    $this->output->error("An error ocured while sending mail to ".$user->getEmail());
                }
            }
        }
        return self::EXITCODE;
    }


    /**
     * @param int $stockId
     *
     * @return array
     */
    private function getMessage(int $stockId)
    {
        $message = '<div>Some of your products are expiring, here is the list of products concerned : <br/><br/></div>';
        $nb = 0;

        foreach ($this->days as $day) {

            switch ($day) {
                case 0:
                    $message .= '<h2>Product expired</h2>';
                    break;
                case 3:
                    $message .= '<h2>Product that will expire in 3 days</h2>';
                    break;
            }

            $stockProducts = $this->stockProductsRepository->findDateExpires(null,$stockId, $day);
            $message .= '<ul>';

            /** @var StockProducts $stockProduct */
            foreach ($stockProducts as $stockProduct) {

                    /** @var Product $product */
                    $product = $stockProduct->getProduct();

                    $message .= '<li>'
                        .$stockProduct->getDateExpire()->format(SiteConfig::DATELOCALE['en'])
                        .' - '.$product->getName()
                        .' '.$product->getBrands()[0]->getName()
                        .' '. $product->getQuantity()
                        .'</li>';
                    $nb++;
                 }
            $message .= '</ul>';
        }

        return ['nb' => $nb, 'message' => $message];
    }

    /**
     * @param User $user
     * @param array $messageData
     *
     * @return int
     */
    private function sendMail(User $user, Array $messageData) : int
    {
        return $this->mailer->send(
            SiteConfig::SECURITYMAIL,
            $user->getEmail(),
            $this->twig->render('mail/stock/expired.html.twig', array('message' => $messageData['message'])),
            $this->twig->render('mail/stock/expired.txt.twig', array('message' => $messageData['message'])),
            SiteConfig::SITENAME.' - You have '.$messageData['nb'].' products expiring products'
        );
    }
}
