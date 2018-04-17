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
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class Databasemanager
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
     * @param Application $application
     * @param KernelInterface $kernel
     */
    public function __construct(Application $application,KernelInterface $kernel, ObjectManager $manager)
    {
        // application
        $this->application = $application;

        // kernel
        $this->kernel = $kernel;

        // env
        $this->env = $this->kernel->getEnvironment();

        // get the entity manager
        $this->eManager = $manager;
    }

    public function create()
    {
        $this->dropDatabase();
        $this->createDatabase();
        $this->updateDatabase();
        $this->importCountries();
    }

    public function update()
    {
        $this->updateDatabase();
        $this->importCountries();
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
    private function importCountries()
    {
        //tester si la table est vide
        $this->application->run(new StringInput('doctrine:database:import '.SiteConfig::SQLCOUNTRY.' --env='.$this->env));
    }
}