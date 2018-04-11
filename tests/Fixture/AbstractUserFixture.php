<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Fixture;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
abstract class AbstractUserFixture
{
    /**
     * Check if test are made in test environement
     * @param KernelInterface $kernel
     *
     * @return Application
     */
    public static function checkEnvironement(KernelInterface $kernel) : Application
    {
        // Make sure we are in the test environment
        if ('test' !== $kernel->getEnvironment()) {
            throw new \LogicException('Test must be executed in the test environment');
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);

        return $application;
    }

    /**
     * Recreate the database
     * @param KernelInterface $kernel
     * @throws \Exception
     */
    public static function createDatabase(KernelInterface $kernel) : void
    {
        $application = self::checkEnvironement($kernel);

        $application->run(new StringInput('doctrine:database:drop --force --env=test'));
        $application->run(new StringInput('doctrine:database:create --env=test'));
        $application->run(new StringInput('doctrine:migrations:migrate --env=test --no-interaction'));
    }

    /**
     * Add $amount fixtures into database
     * @param int $amount
     *
     * @return string
     */
    public static function createFixtures(KernelInterface $kernel, int $amount) : void
    {
        // check environement
        self::checkEnvironement($kernel);

        // create a new fake object with fr
        $fake = \Faker\Factory::create('fr_FR');

        // get the entity manager
        $eManager = $kernel->getContainer()->get('doctrine')->getManager();

        for ($i = 0; $i < $amount; $i++) {

            // create new contact
            $user = new User();

            // add fake informations
            $user->setFirstName($fake->firstName())
                ->setLastName($fake->lastName())
                ->setEmail($fake->freeEmail())
                ->setPassword($fake->password());

            // set for ctreate in db
            $eManager->persist($user);
        }

        // add to db
        $eManager->flush();
    }
}