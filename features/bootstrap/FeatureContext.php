<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends MinkContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When a demo scenario sends a request to :path
     */
    public function aDemoScenarioSendsARequestTo(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived()
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @Given /^I start the scenario$/
     */
    public function iStartTheScenario()
    {

    }

    /**
     * @Then /^I recreate database$/
     */
    public function iRecreateDatabase1()
    {
        $application = new Application($this->getKernel());

        $application->setAutoExit(false);

        /*
        $application->run(new StringInput('doctrine:database:drop --force --env=dev'));

        $application->run(new StringInput('doctrine:database:create --env=dev'));

        $application->run(new StringInput('doctrine:migrations:migrate --no-interaction --env=dev'));
        */
    }

    /**
     * @When I wait :duration sec
     */
    public function iWaitSec($duration)
    {
        $this->getSession()->wait($duration * 1000);
    }

    /**
     * @Given /^Contact "([^"]*)" should be in database$/
     */
    public function contactShouldBeInDatabase($email)
    {
        //$contact = $this->getContainer()->get('doctrine')->getRepository(Contact::class)->findBy(['email' => $email ]);
        $contact = $this->getContainer()->get('doctrine')->getRepository(Contact::class)->findOneByEmail($email);

        $nbContact = $this->getContainer()->get('doctrine')->getRepository(Contact::class)->countByEmail($email);

        if(!$contact) {
            throw new Exception('Email '.$email.' not found in database');
        }

        if($nbContact!==1) {
            throw new Exception($nbContact .'Email '.$email.' found in database');
        }

    }

}
