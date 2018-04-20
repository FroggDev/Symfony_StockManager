<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Stock;

use App\Common\Traits\Product\FolderTrait;
use App\Entity\User;
use App\SiteConfig;
use Goutte\Client;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use App\Exception\Product\ProductTypeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * TODO : Missing fields
 * nutriscore
 */


/**
 * @author Frogg <admin@frogg.fr>
 */
class ProductScraper
{
    use FolderTrait;

    /** @var Client */
    private $client;

    /** @var Crawler */
    private $crawler;

    /** @var EntityManagerInterface */
    private $manager;

    /** @var User  */
    private $user;

    /** @var Product */
    private $product;

    /** @var string */
    private $barcode;

    /**
     * Map between INPUT, DATA TYPE, ENTITY PROPERTY, LINKED ENTITY
     * @var array
     */
    private $map = [
        ['#product_name_fr','string', 'name'],
        ['#generic_name_fr','string', 'commonName'],
        ['#ingredients_text_fr','string', 'ingredients'],
        ['#quantity','string', 'quantity'],
        ['#emb_codes','string', 'embCode'],
        ['#link','string', 'producerPage'],
        ['#packaging','array', 'packagings', 'App\Entity\Product\Packaging'],
        ['#brands','array', 'brands', 'App\Entity\Product\Brand'],
        ['#categories','array', 'categories', 'App\Entity\Product\Category'],
        ['#labels','array', 'certifications', 'App\Entity\Product\Certification'],
        ['#origins','array', 'origins', 'App\Entity\Product\Origin'],
        ['#manufacturing_places','array', 'places', 'App\Entity\Product\Place'],
        ['#countries','array', 'countries', 'App\Entity\Country'],
        ['#traces','array', 'traces', 'App\Entity\Product\Trace'],

        ['#nutriment_energy','float','energy'],
        ['#nutriment_fat','float','levelFat'],
        ['#nutriment_saturated-fat','float','levelSaturedFat'],
        ['#nutriment_carbohydrates','float','levelCarbohydrate'],
        ['#nutriment_sugars','float','levelSugar'],
        ['#nutriment_fiber','float','levelDietaryFiber'],
        ['#nutriment_proteins','float','levelProteins'],
        ['#nutriment_salt','float','levelSalt'],
        ['#nutriment_sodium','float','levelSodium'],
        ['#nutriment_alcohol','float','levelAlcohol'],
        ['#nutriment_silica','float','levelSilica'],
        ['#nutriment_bicarbonate','float','levelBicarbonate'],
        ['#nutriment_potassium','float','levelPotassium'],
        ['#nutriment_chloride','float','levelChloride'],
        ['#nutriment_calcium','float','levelCalcium'],
        ['#nutriment_magnesium','float','levelMagnesium'],
        ['#nutriment_nitrates','float','levelNitrates'],
        ['#nutriment_sulfates','float','levelSulfates'],

        ['#nutriment_energy_unit','string','energyUnit'],
        ['#nutriment_fat_unit','string','levelFatUnit'],
        ['#nutriment_saturated-fat_unit','string','levelSaturedFatUnit'],
        ['#nutriment_carbohydrates_unit','string','levelCarbohydrateUnit'],
        ['#nutriment_sugars_unit','string','levelSugarUnit'],
        ['#nutriment_fiber_unit','string','levelDietaryFiberUnit'],
        ['#nutriment_proteins_unit','string','levelProteinsUnit'],
        ['#nutriment_salt_unit','string','levelSaltUnit'],
        ['#nutriment_sodium_unit','string','levelSodiumUnit'],
        ['#nutriment_alcohol_unit','string','levelAlcoholUnit'],
        ['#nutriment_silica_unit','string','levelSilicaUnit'],
        ['#nutriment_bicarbonate_unit','string','levelBicarbonateUnit'],
        ['#nutriment_potassium_unit','string','levelPotassiumUnit'],
        ['#nutriment_chloride_unit','string','levelChlorideUnit'],
        ['#nutriment_calcium_unit','string','levelCalciumUnit'],
        ['#nutriment_magnesium_unit','string','levelMagnesiumUnit'],
        ['#nutriment_nitrates_unit','string','levelNitratesUnit'],
        ['#nutriment_sulfates_unit','string','levelSulfatesUnit'],

        ['#nutriment_carbon-footprint','float','footprint'],
        ['#nutriment_carbon-footprint_unit','string','footprintUnit']
    ];

    private $imageSelector = '#front_fr_display_url';

    /**
     * ProductScraper constructor.
     * @param Client $client
     * @param EntityManagerInterface $manager
     * @param TokenStorage $storage
     */
    public function __construct(Client $client, EntityManagerInterface $manager,TokenStorageInterface $storage)
    {
        $this->client = $client;
        $this->manager = $manager;
        $this->user = $storage->getToken()->getUser();
    }

    /**
     * @param string $barcode
     *
     * @return Product|null
     * @throws ProductTypeException
     */
    public function scrap(string $barcode) : ?Product
    {

        //set barcode
        $this->barcode=$barcode;

        //init product
        $this->product = new Product((int) $barcode);

        // add user creator product
        $this->product->setUser($this->user);

        // do the navigation
        $this->navigate();

        // check if as reult
        if(false===$this->hasResult()){
            return null;
        }

        // fill the product from navigation pages
        $this->fillProduct();

        // get the product image
        $this->getImageProduct();

        // save to database
        $this->manager->persist($this->product);
        $this->manager->flush();

        // return the save product
        return $this->product;
    }

    /**
     * Make the nagivation to get product page content
     */
    private function navigate() : void
    {
        //set follow redirect
        $this->client->followRedirects(true);

        // create a new crawler to login on website
        $this->client->request(
            'POST',
            SiteConfig::SCRAPLOGINURL,
            [
                SiteConfig::SCRAPINPUTUSERID => SiteConfig::SCRAPUSERID,
                SiteConfig::SCRAPINPUTPASSWORD => SiteConfig::SCRAPPASSWORD
            ]
        );

        // get the product informations on the website
        $this->crawler = $this->client->request(
            'GET',
            SiteConfig::SCRAPDATAURL . $this->product->getBarcode()
        );
    }

    /**
     * download the image product and update product info
     */
    private function getImageProduct()
    {
        // get image name
        $imageName = $this->crawler->filter($this->imageSelector)->eq(0)->attr('value');

        //set target image name
        $image = $this->barcode.'.'.pathinfo($imageName, PATHINFO_EXTENSION);

        // get product image path
        $imageFolder = $this->getFolder($this->barcode);

        //create the local folder
        $localFolder = SiteConfig::UPLOADPATH.$imageFolder;
        @mkdir($localFolder, 0777, true);

        // get product image full path
         file_put_contents(
             $localFolder.$image,
             file_get_contents(SiteConfig::SCRAPIMGURL . $imageFolder .$imageName)
         );

        //set image in product object
        $this->product->setPicture($image);
    }


    /**
     * @return bool
     */
    private function hasResult():bool
    {
        return $this->crawler->filter('H1')->eq(0)->text()!=="Erreur";
    }

    /**
     * set the product information
     * @throws ProductTypeException
     */
    private function fillProduct() : void
    {
        foreach($this->map as $line){

            //get value from input
            $value = $this->getValue($line);

            // if node is empty then continue to next element
            if($value===null || $value===""){
                continue;
            }

            // get value with correct type
            $value = $this->formatValue($value,$line);

            //prepare action (dynamic setter)
            $action='set'.ucfirst($line[2]);

            //Add data to product
            $this->product->$action($value);
        }
    }

    /**
     * @param array $line
     *
     * @return null|string
     */
    private function getValue(array $line) : ?string
    {
        if(false!==strstr ($line[0],'_unit')) {
            //get data from a select
            $node = $this->crawler->filter($line[0] . ' option:selected');
            return $node->count() ? $node->eq(0)->text() : null;
        }

        // get datas from other inputs
        $node = $this->crawler->filter($line[0]);
        return $node->count() ? $node->eq(0)->attr('value') : null;
     }

    /**
     * Format the value to fit the database specs
     * @param string $value
     * @param array $line
     *
     * @return array|float|int|string
     *
     * @throws ProductTypeException
     */
    private function formatValue(string $value, array $line)
    {
        // link to entity
        if(count($line)===4){

            //get the list of values as string
            $values = array_map('trim',explode(",",$value));

            // get the value as array of object
            $arrayValue = $this->getEntityValue($values,$line[3]);
        }

        // Convert to required type
        switch($line[1]){
            case 'array':
                return $arrayValue;
            case 'string':
                return $value;
            case 'int':
                return (int) $value;
            case 'float':
                return (float) $value;
            default:
                throw new ProductTypeException('invalid type ' . $line[2]);
        }
    }

    /**
     * Get or set the linked entities
     * @param array $values
     * @param string $entity
     *
     * @return array
     */
    private function getEntityValue(array $values, string $entity) : array
    {
        //list of items
        $items = [];

        foreach($values as $value){

            //if no values continues
            if($value===null || $value===""){
                continue;
            }

            //get value (cleaned)
            $value = str_replace('fr:','',$value);

            //get item in database
            $item = $this->manager->getRepository(get_class(new $entity))->findOneByName(ucfirst($value));

            //If not found create it
            if(!$item){
                $item = new $entity($value);
            }

            //add it to the list of items
            $items[]=$item;
        }

        return $items;
    }
}