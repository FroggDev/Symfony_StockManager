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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Symfony console
 * @see https://symfony.com/doc/current/console.html
 */
class ProductCommand extends Command
{

    /** @const int EXITCODE the normal code returned when exit the command */
    public const EXITCODE = 0;

    /** @var SymfonyStyle */
    private $output;

    /** @var \App\Service\Stock\ProductManager */
    private $productManager;

    /**
     * UserRoleManager constructor.
     * @param \App\Service\Stock\ProductManager $productManager
     */
    public function __construct(\App\Service\Stock\ProductManager $productManager)
    {
        // parent constructor
        parent::__construct();

        $this->productManager = $productManager;
    }

    /**
     * Set the command name/description/help
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:product:add')
            // the short description shown while running "php bin/console list"
            ->setDescription('add product from barcode.')
            ->addArgument(
                'barcode',
                InputArgument::REQUIRED|InputArgument::IS_ARRAY,
                'Which barcode do you want to add ?'
            )
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to scrap a product from barcode and save to database...');
    }

    /**
     * Main function
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // INIT STYLES
        $this->output = new SymfonyStyle($input, $output);

        // Get parameters
        $barcodes = $input->getArgument('barcode');

        // DO MAIN SCRIPT
        $this->addProducts($barcodes);
    }

    /**
     * @param array $barcodes
     * @throws \App\Exception\Product\ProductTypeException
     */
    private function addProducts(array $barcodes)
    {
        foreach ($barcodes as $barcode) {
            $resultString = $this->productManager->getProductFromBarcode($barcode);

            $result = json_decode($resultString);

            if ("ok" !== $result->result) {
                $this->output->error("An error occured while trying to scrap $barcode");
                continue;
            }

            if (!isset($result->name)) {
                $this->output->warning("$barcode cannot be found");
                continue;
            }

            $this->output->success($result->name . " has been added to database from barcode $barcode ");
        }

        return self::EXITCODE;
    }
}
