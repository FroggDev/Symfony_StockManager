<?php

use App\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
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
    use KernelDictionary;

    /** @const int time to wait for demo */
    private const DEMOTIME = 0;

    /** @const string where screenshots are saved */
    private const SCREENSHOT = 'C:\symfony\Symfony_StockManager\public\output\behat\screenshot';

    /** @const string where error screenshots are saved */
    private const SCREENSHOTERR = 'C:\symfony\Symfony_StockManager\public\output\behat\assets\screenshots';

    /** @var int incremental */
    private $idScreenshot = 0;

    /** @var KernelInterface */
    private $kernel;

    /** @var Response|null */
    private $response;

    /**
     * FeatureContext constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When a demo scenario sends a request to :path
     */
    /*
    public function aDemoScenarioSendsARequestTo(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }
    */

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     *
     */
    public function setUpTestEnvironment($scope)
    {
        $this->currentScenario = $scope->getScenario();
    }

    /**
     * @AfterStep
     *
     * @param AfterStepScope $scope
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function afterStep($scope)
    {

        // This is trigger when scenario is not javascript, no browsers are opened
        if (!($this->getSession()->getDriver() instanceof Selenium2Driver)) {
            // throw new UnsupportedDriverActionException('Taking screenshots is not supported by %s, use Selenium2Driver instead.', $driver);
            return;
        }

        //remove alerts if exist
        try {
            $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
        } catch (\Exception $exception) {}

        //if test has failed, and is not an api test, get screenshot
        if (!$scope->getTestResult()->isPassed()) {
            $this->takeAScreenshot(
                self::SCREENSHOTERR,
                preg_replace('/\W/', '', $scope->getFeature()->getTitle()),
                preg_replace('/\W/', '', $this->currentScenario->getTitle()) . '.png'
            );
        } else {
            $this->idScreenshot++;
            $this->takeAScreenshot(
                self::SCREENSHOT,
                preg_replace('/\W/', '', $scope->getFeature()->getTitle()),
                preg_replace('/\W/', '', $this->currentScenario->getTitle()) . '-' . $this->idScreenshot . '.png'
            );
        }
    }

    /**
     * @Given /^I take a screenshot "([^"]*)"$/
     * @param string $filename
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function iTakeAScreenshot(string $filename)
    {
        $this->takeAScreenshot(
            self::SCREENSHOT,
            '_custom',
            $filename
        );
    }

    /**
     * @Then /^Wait for demo$/
     */
    public function waitForDemo()
    {
        $this->getSession()->wait(self::DEMOTIME * 1000);
    }

    /**
     * @Then the response :code should be received
     * @param $code
     */
    public function theResponseShouldBeReceived($code): void
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }

        if ($this->response->getStatusCode() == $code) {
            throw new \RuntimeException('Wrong status code, expecting : ' . $code);
        }
    }

    /**
     * @Given /^I set browser window size to "([^"]*)" x "([^"]*)"$/
     * @param $width
     * @param $height
     */
    public function iSetBrowserWindowSizeToX($width, $height): void
    {
        $this->getSession()->restart();
        $this->getSession()->resizeWindow((int)$width, (int)$height, 'current');
    }

    /**
     * @Given /^I start the scenario$/
     */
    public function iStartTheScenario()
    {

    }

    /**
     * @Then /^I recreate database$/
     * @throws Exception
     */
    public function iRecreateDatabase(): void
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $application->run(new StringInput('doctrine:database:drop --quiet --force --env=dev'));
        $application->run(new StringInput('doctrine:database:create --quiet --env=dev'));
        $application->run(new StringInput('doctrine:migrations:migrate --quiet --no-interaction --env=dev'));
    }

    /**
     * @When I wait :duration sec
     * @param $duration
     */
    public function iWaitSec($duration): void
    {
        $this->getSession()->wait($duration * 1000);
    }

    /**
     * @Given /^User "([^"]*)" should be "([^"]*)" in database$/
     * @param string $email
     * @param string $status
     * @throws Exception
     */
    public function userShouldBeInDatabase(string $email, string $status): void
    {
        /** @var User $user */
        $user = $this->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail($email);

        $nbUser = $this->getContainer()->get('doctrine')->getRepository(User::class)->countByEmail($email);

        # check if user is found
        if (!$user) {
            throw new Exception('Email ' . $email . ' not found in database');
        }

        # check if there is more than 1 user with same email in database
        if ($nbUser !== 1) {
            throw new Exception($nbUser . 'Email ' . $email . ' found in database');
        }

        # check user status
        /*
        $status = "is${status}()";
        if (!$user->$status) {
            throw new Exception('user ' . $email . ' should be ' . $status);
        }
        */

        if ("Enabled" === $status) {
            if (!$user->isEnabled()) {
                throw new Exception('user ' . $email . ' should be ' . $status);
            }
        }

        if ("Disabled" === $status) {
            if (!$user->isDisabled()) {
                throw new Exception('user ' . $email . ' should be ' . $status);
            }
        }
    }

    /**
     * @When /^I click on selector "([^"]*)"$/
     *
     * @param $css
     */
    public function iClickSelector($css): void
    {
        $this->getElementFromXpath($css)->click();
    }

    /**
     * @Given /^I open an iframe on Selector "([^"]*)"$/
     * @param $css
     */
    public function iOpenAnIframeSameServer($css): void
    {
        $this->iOpenAnIframe($css);
    }

    /**
     * @Given /^I open an iframe on Selector "([^"]*)" from server "([^"]*)"$/
     * @param $css
     * @param null $server
     */
    public function iOpenAnIframe($css, $server = null): void
    {
        $this->visit($server . $this->getElementFromXpath($css)->getAttribute('src'));
    }


    /**
     * @param $css
     * @return \Behat\Mink\Element\NodeElement|mixed|null
     */
    private function getElementFromXpath($css)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('css', $css)
        );

        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find xpath: "%s"', $css));
        }

        return $element;
    }


    /**
     * @Then /^I switch tab$/
     */
    public function iSwitchTab(): void
    {
        $windowNames = $this->getSession()->getWindowNames();
        if (count($windowNames) > 1) {
            $this->getSession()->switchToWindow($windowNames[1]);
        }
    }

    /**
     * @hidden
     *
     * @Then /^I confirm$/
     */
    public function iClickOnTheAlertWindow()
    {
        //remove alerts if exist
        try {
            $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
        } catch (\Exception $exception) {
        }
    }

    /**
     * @Given /^Input "([^"]*)" should be fill with "([^"]*)"$/
     * @throws Exception
     */
    public function inputShouldBeFillWith(string $css, string $value)
    {
        $inputValue = $this
            ->getMink()
            ->getSession()
            ->getPage()
            ->find('css', $css)
            ->getAttribute('value');

        if ($inputValue != $value) {
            throw new Exception("input $css value should be $value instead of $inputValue");
        }
    }

    /**
     * @Given /^cookie "([^"]*)" should be fill with "([^"]*)"$/
     * @throws Exception
     */
    public function cookieShouldBe(string $name, string $value)
    {
        if ($value !== $this->getSession()->getCookie($name)) {
            throw new Exception("cookie $name should be fill with $value but value is " . $this->getSession()->getCookie($name));
        }
    }

    /**
     * @When /^I set "([^"]*)" token as expired with value "([^"]*)"$/
     * @param string $email
     * @param string $token
     */
    public function iSetTokenAsExpiredWithValue(string $email, string $token)
    {
        $now = new \DateTime();

        /** @var \Doctrine\ORM\EntityManagerInterface */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

        $user->setToken($token);

        $user->testToSetTokenValidity($now->modify('-1 day'));

        // insert into database
        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @Then /^I delete old screenshots$/
     */
    public function iDeleteOldScreenshots()
    {
        $this->delTree(self::SCREENSHOT);
        $this->delTree(self::SCREENSHOTERR);
    }

    /**
     * @param string $path
     * @param string $folder
     * @param string $fileName
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    private function takeAScreenshot(string $path, string $folder, string $fileName)
    {
        $driver = $this->getSession()->getDriver();

        // This is trigger when scenario is not javascript, no browsers are opened
        if (!($driver instanceof Selenium2Driver)) {
            // throw new UnsupportedDriverActionException('Taking screenshots is not supported by %s, use Selenium2Driver instead.', $driver);
            return;
        }

        //create filename string
        $featureFolder = preg_replace('/\W/', '', $folder);

        //create screenshots directory if it doesn't exist
        if (!file_exists($path . '\\' . $featureFolder)) {
            mkdir($path . '\\' . $featureFolder, 0777, true);
        }

        //take screenshot and save as the previously defined filename
        // $this->driver->takeScreenshot($path .'\\' . $featureFolder . '\\' . $fileName);
        // For Selenium2 Driver you can use:
        file_put_contents(
            $path . '\\' . $featureFolder . '\\' . $fileName,
            $driver->getScreenshot()
        );
    }

    /**
     * @param string $dir
     * @return bool
     */
    private function delTree(string $dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : @unlink("$dir/$file");
        }
        return @rmdir($dir);
    }

}
