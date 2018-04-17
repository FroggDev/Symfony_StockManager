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
namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @ORM\Entity()
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Origin
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
