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
 * php bin/console app:database [create,update,delete]
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

use App\SiteConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Symfony console
 * @see https://symfony.com/doc/current/console.html
 *
 * TODO : MANAGE RETURN CODE WHEN ERROR
 */
class DatabaseCommand extends Command
{

    /** @const int EXITCODE the normal code returned when exit the command */
    public const EXITCODE = 0;

    /** @var SymfonyStyle */
    private $output;

    /** @var string */
    private $env;

    /** @var \App\Service\DatabaseManager */
    private $databaseManager;

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
            ->setName('app:database')
            // the short description shown while running "php bin/console list"
            ->setDescription('Manage database')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'Which action do you want to do ?'
            )
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to manage the database, action available are [create,update,delete] ...');
    }

    /**
     * Main function
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // INIT VARS
        $kernel = $this->getApplication()->getKernel();

        $this->databaseManager = $kernel
            ->getContainer()
            ->get('app.service.database_manager');

        $this->env = $kernel->getEnvironment();

        // INIT STYLES
        $this->output = $output;//new SymfonyStyle($input, $output);

        // Get parameters
        $action = $input->getArgument('action');

        // DO MAIN SCRIPT
        switch ($action) {
            case 'create':
                $this->create();
                break;
            case 'update':
                $this->update();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                // ERROR COLOR
                //$this->output->warning('The action '.$action.' is not defined... exiting command');
        }

        return self::EXITCODE;
    }

    /**
     * @throws \Exception
     */
    private function create()
    {
        $this->delete();
        $this->createDatabase();
        $this->updateDatabase();
        $this->importData();
    }

    /**
     * @throws \Exception
     */
    private function update()
    {
        $this->updateDatabase();
    }

    /**
     * @throws \Exception
     */
    private function delete()
    {
        $this->dropDatabase();
    }

    /**
     * Drop the database
     * @throws \Exception
     */
    private function dropDatabase(): void
    {
        $arguments = array(
            'command' => 'doctrine:database:drop',
            '--force'    => true,
            //'--quiet'  => true,
            '--env'  => $this->env,
        );
        $this->doCommand('doctrine:database:drop', $arguments);
    }

    /**
     * Create the database
     * @throws \Exception
     */
    private function createDatabase(): void
    {
        $arguments = array(
            'command' => 'doctrine:database:create',
            '--env'  => $this->env,
        );
        $this->doCommand('doctrine:database:create', $arguments);
    }

    /**
     * Update the database
     * @throws \Exception
     */
    private function updateDatabase(): void
    {
        $arguments = array(
            'command' => 'doctrine:migrations:migrate',
            '--env'  => $this->env,
            '--quiet' => true,
            '--no-interaction' => true
        );
        $this->doCommand('doctrine:migrations:migrate', $arguments);
    }

    /**
     * Import countries in database
     * @throws \Exception
     */
    private function importData()
    {
        $arguments = array(
            'command' => 'doctrine:database:import',
            '--env'  => $this->env,
            'file' => SiteConfig::SQLCOUNTRY
        );
        $this->doCommand('doctrine:database:import', $arguments);

        $arguments = array(
            'command' => 'doctrine:database:import',
            '--env'  => $this->env,
            'file' => SiteConfig::SQLSTOCK
        );
        $this->doCommand('doctrine:database:import', $arguments);

        $arguments = array(
            'command' => 'doctrine:database:import',
            '--env'  => $this->env,
            'file' => SiteConfig::SQLUSER
        );
        $this->doCommand('doctrine:database:import', $arguments);
    }


    /**
     * @param string $commandString
     * @param array $arguments
     *
     * @return int
     *
     * @throws \Exception
     */
    private function doCommand(string $commandString, array $arguments)
    {
        $command = $this->getApplication()->find($commandString);

        $commandInput = new ArrayInput($arguments);

        return $command->run($commandInput, $this->output);
    }
}
