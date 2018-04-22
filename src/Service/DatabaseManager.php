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

use App\SiteConfig;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class DatabaseManager
{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $env;

    /**
     * @var EntityManager
     */
    private $eManager;

    /**
     * ImportDatabase constructor.
     * @param KernelInterface        $kernel
     * @param EntityManagerInterface $manager
     */
    public function __construct(KernelInterface $kernel, EntityManagerInterface $manager)
    {
        // application
        $this->application = new Application($kernel);

        // kernel
        $this->kernel = $kernel;

        // env
        $this->env = $this->kernel->getEnvironment();

        // get the entity manager
        $this->eManager = $manager;
    }

    /**
     * Recreate fully the database
     * @throws \Exception
     */
    public function create()
    {
        $this->delete();
        $this->createDatabase();
        $this->updateDatabase();
        $this->importData();
    }

    /**
     * remove database
     */
    public function delete()
    {
        try {
            $this->dropDatabase();
        } catch (\Exception $exception) {
            echo 'Error while deleting database : '.$exception->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    public function update()
    {
        $this->updateDatabase();
    }

    /**
     * Drop the database
     * @throws \Exception
     */
    private function dropDatabase(): void
    {
        $this->application->run(new StringInput('doctrine:database:drop --force --env='.$this->env));
    }

    /**
     * Create the database
     * @throws \Exception
     */
    private function createDatabase(): void
    {
        $this->application->run(new StringInput('doctrine:database:create --env='.$this->env));
    }

    /**
     * Update the database
     * @throws \Exception
     */
    private function updateDatabase(): void
    {
        $this->application->run(new StringInput('doctrine:migrations:migrate --no-interaction --env='.$this->env));
    }

    /**
     * Import countries in database
     * @throws \Exception
     */
    private function importData()
    {
        //tester si la table est vide
        $this->application->run(new StringInput('doctrine:database:import '.SiteConfig::SQLCOUNTRY.' --env='.$this->env));
        $this->application->run(new StringInput('doctrine:database:import '.SiteConfig::SQLSTOCK.' --env='.$this->env));
        $this->application->run(new StringInput('doctrine:database:import '.SiteConfig::SQLUSER.' --env='.$this->env));
    }
}
