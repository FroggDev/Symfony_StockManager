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
 * php bin/console app:userManager
 *
 * require Constant :
 * ------------------
 * SiteConfig::SECURITYROLES
 * SiteConfig::USERENTITY
 * TODO : Get This dynamically from Security.yaml
 * security.providers.author_provider.entity.class
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

 * News in 4.1 :
 * -------------
https://symfony.com/blog/new-in-symfony-4-1-advanced-console-output

// Creating section
$section = $output->section();
$section->writeln('Downloading the file...');
$section->overwrite('Uncompressing the file...');
$section->overwrite('Copying the contents...');

// Cleaning section last line
$section->clear(1);
//cleaning all section
$section->clear();

// Dynamic table management
$table = new Table($section2);
$table->addRow(['Row 1']);
$table->render();
 */

namespace App\Command;

use App\Entity\User;
use App\SiteConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Symfony console
 * @see https://symfony.com/doc/current/console.html
 */
class UserCommand extends Command
{
    /** @const int EXITCODE the normal code returned when exit the command */
    public const EXITCODE = 0;

    /** @const int CONTINUECODE the code returned by method to tell the command to continue its job */
    public const CONTINUECODE = -1;

    /** @var EntityManagerInterface */
    private $eManager;

    /** @var SymfonyStyle */
    private $output;

    /** @var OutputInterface */
    private $defaultOutput;

    /** @var array */
    private $roles;

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
            ->setName('app:userManager')
            // the short description shown while running "php bin/console list"
            ->setDescription('manager users.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to manage users and save to database...');
    }

    /**
     * Main function
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // Use to test 4.1 stuff
        $this->defaultOutput = $output;

        // INIT STYLES
        $this->output = new SymfonyStyle($input, $output);

        // DISPLAY TITLE
        $this->output->title("Welcome to User Role Manager");

        // get doctrine entity manager
        $this->eManager = $this
            ->getApplication()
            ->getKernel()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        // get all available roles
        $this->roles = SiteConfig::SECURITYROLES;

        // DISPLAY MAIN MENU
        $this->displayMainMenu();
    }

    /**
     * Display the command menu
     *
     * @return int
     */
    private function displayMainMenu(): int
    {

        while (true) {
            // Ask user for a choice
            $input = $this->output->choice(
                'Select an action',
                [
                    'Display user list',
                    'Display user list (Version 4.1)',
                    'Enable user account (require user id)',
                    'Add user role (require user id)',
                    'Remove user role (require user id)',
                    'Exit',
                ],
                'Display user list'
            );

            // Action from choice
            switch ($input) {
                case 'Display user list':
                    $code = $this->displayUserList();
                    break;
                case 'Display user list (Version 4.1)':
                    $code = $this->displayUserListNew();
                    break;
                case 'Enable user account (require user id)':
                    $code = $this->enableUser();
                    break;
                case 'Add user role (require user id)':
                    $code = $this->addUserRole();
                    break;
                case 'Remove user role (require user id)':
                    $code = $this->removeUserRole();
                    break;
                case 'Exit':
                    return self::EXITCODE;
            }

            if ($code === self::EXITCODE) {
                return self::EXITCODE;
            }
        }
    }

    /**
     * Display the user list
     *
     * @return int
     */
    private function displayUserList(): int
    {
        // GET USER INFOS
        $entity = SiteConfig::USERENTITY;
        $userList = $this->eManager->getRepository(get_class(new $entity()))->findAll();

        // TEST ONLY
        //echo "TRYING TO ENABLE THE USER";
        //$userTest = $this->eManager->getRepository(get_class(new $entity()))->find(1);
        //$userTest->setEnabled();

        // GET NB USER
        $nbUser = count($userList);

        // Display range information
        $nbLoop = 0;
        $range = 5;

        // As long there is user and answer Yes to the choice
        while (true) {
            //set ranges
            $start = $nbLoop * $range;
            $end = $start + $range;

            //display current range
            $this->displayUserListRange($userList, $start, $end > $nbUser ? $nbUser : $end);

            // Exit if all user was displayed
            if ($end >= $nbUser) {
                break;
            }

            // Ask user to continue
            $input = $this->output->choice('Continue displaying user list ?', ['No', 'Yes'], 'Yes');
            if ("No" === $input) {
                break;
            }
            //set next loop value
            $nbLoop++;
        }

        return self::CONTINUECODE;
    }

    /**
     * @param $userList
     * @param $start
     * @param $end
     */
    private function displayUserListRange($userList, $start, $end): void
    {
        // init display
        $display = [];

        // PREPARE USERS INFOS
        for ($i = $start; $i < $end; $i++) {
            $display[] = [
                $userList[$i]->getId(),
                $userList[$i]->getEmail(),
                $userList[$i]->isEnabled(),
                implode("+", $userList[$i]->getRoles() ?? []),
            ];
        }

        // DISPLAY USER LIST AS TABLE

        $this->output->title('Displaying user list using SymfonyStyle !');

        $this->output->table(['ID', 'EMAIL', 'ENABLED', 'ROLES'], $display);
    }


    /**
     * Display the user list (version 4.1)
     *
     * @return int
     */
    private function displayUserListNew(): int
    {

        $this->output->title('Displaying user list using 4.1 feature !');

        // Create a 4.1 section block
        $section = $this->defaultOutput->section();
        $sectionTable = $this->defaultOutput->section();

        // Display stuff
        $section->writeln('');
        $section->writeln('Loading user list...');

        // GET USER INFOS
        $entity = SiteConfig::USERENTITY;
        $userList = $this->eManager->getRepository(get_class(new $entity()))->findAll();

        // Dynamic table from 4.1
        $table = new Table($sectionTable);

        //Table headers
        $table->addRow(['ID', 'EMAIL', 'ENABLED', 'ROLES']);

        // set max value to progress bar
        $progress = new ProgressBar($section);
        $progress->start(count($userList));

        // Add User info
        foreach ($userList as $user) {

            /** @var User $user */
            $table->appendRow(
                [
                    $user->getId(),
                    $user->getEmail(),
                    $user->isEnabled(),
                    implode("+", $user->getRoles() ?? []),
                ]
            );

            $progress->advance();
            sleep(1);
        }
        $section->clear();

        return self::CONTINUECODE;
    }


    /**
     * Enable an user
     *
     * @return int
     */
    private function enableUser(): int
    {
        // get user id from input
        $user = $this->askUserId();

        // if user not found exit
        if (!$user) {
            return self::CONTINUECODE;
        }

        // set user as active
        $user->setEnabled();
        // save to database
        $this->eManager->flush();

        // OK COLOR
        $this->output->success("The user '".$user->getId()."' has been enabled");

        return self::CONTINUECODE;
    }

    /**
     * Add a role to an user
     *
     * @return int
     */
    private function addUserRole(): int
    {
        // get user id from input
        $user = $this->askUserId();

        // if user not found exit
        if (!$user) {
            return self::CONTINUECODE;
        }

        // Add options
        $userRolesDisplay = array_diff($this->roles, $user->getRoles() ?? []);
        $userRolesDisplay[] = "Cancel";
        $userRolesDisplay[] = "Exit";

        // recalculate array keys
        $userRolesDisplay = array_values($userRolesDisplay);

        // Check if can add role
        if (2 === count($userRolesDisplay)) {
            $this->output->warning("No role can be added from user with id '".$user->getId());

            return self::CONTINUECODE;
        }

        // Ask user for a choice
        $input = $this->output->choice('Select a role to add', $userRolesDisplay, 'Cancel');

        if ('Cancel' === !$input) {
            return self::CONTINUECODE;
        }

        switch ($input) {
            case "Cancel":
                return self::CONTINUECODE;

            case "Exit":
                return self::EXITCODE;

            default:
                // role already exist
                if ($user->hasRole($input)) {
                    // ERROR COLOR
                    $this->output->warning("The user already has the role ".$input);

                    return self::CONTINUECODE;
                }

                // update user role
                $user
                    ->setRoles($input)
                    ->setEnabled();
                // save to database
                $this->eManager->flush();

                // OK COLOR
                $this->output->success("The role '$input' has been added to user with id '".$user->getId()."'");
                break;
        }


        return self::CONTINUECODE;
    }

    /**
     * Remove a role to an user
     *
     * @return int
     */
    private function removeUserRole(): int
    {
        // get usserid from input
        $user = $this->askUserId();

        // if user not found exit
        if (!$user) {
            return self::CONTINUECODE;
        }

        $userRoles = $user->getRoles() ?? [];

        // Add options
        $userRolesDisplay = $userRoles;
        $userRolesDisplay[] = "Cancel";
        $userRolesDisplay[] = "Exit";

        // Check if can remove role
        if (2 === count($userRolesDisplay)) {
            $this->output->warning("No role can be removed from user with id '".$user->getId()."'");

            return self::CONTINUECODE;
        }

        // Ask user for a choice
        $input = $this->output->choice('Remove a role from the user '.$user->getId(), $userRolesDisplay);

        if ("Cancel" === $input) {
            return self::CONTINUECODE;
        }

        if ("Exit" === $input) {
            return self::EXITCODE;
        }

        // update user info
        if (($key = array_search($input, $userRoles)) !== false) {
            unset($userRoles[$key]);
        }
        $user->setAllRoles($userRoles);

        // save role to database
        $this->eManager->flush();

        // OK COLOR
        $this->output->success("The role '$input' has been removed from user with id '".$user->getId()."'");

        return self::CONTINUECODE;
    }

    /**
     * Ask an ID user
     *
     * @return null|object
     */
    private function askUserId() : User
    {
        // Ask for user select
        $input = $this->output->ask('Select an id user');

        $entity = SiteConfig::USERENTITY;
        $user = $this->eManager->getRepository(get_class(new $entity()))->find($input);

        if (!$user) {
            // ERROR COLOR
            $this->output->error("User id '$input' not found !");

            return null;
        }

        return $user;
    }
}
