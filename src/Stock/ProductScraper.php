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

use App\Entity\User;
use Goutte\Client;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use App\Exception\Product\ProductTypeException;

/**
 * @author Frogg <admin@frogg.fr>
 */
class ProductScraper
{
    /** @var Client */
    private $client;

    /** @var Crawler */
    private $crawler;

    /** @var Product */
    private $product;

    /** @var array  */
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
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * ProductScraper constructor.
     * @param Client $client
     * @param EntityManagerInterface $manager
     */
    public function __construct(Client $client, EntityManagerInterface $manager)
    {
        $this->client = $client;
        $this->manager = $manager;
    }

    /**
     * @param string $barcode
     *
     * @return Product
     */
    public function scrap(string $barcode) : Product
    {
        //init product
        $this->product = new Product(intval($barcode));

        /**
         * TODO IF PRODUCT IS NOT FOUND
         * ADD USER ID
         */
        $fakeuser = new User();
        $fakeuser
            ->setFirstName('FIRST')
            ->setLastName('LAST')
            ->setEmail('email@frogg.fr')
            ->setPassword('FAKE PASS');

        $this->product->setUser($fakeuser);

        // do the navigation
        $this->navigate();

        // fill the product from navigation pages
        $this->fillProduct();

        /*
        dump($this->product);
        exit();
        */

        // save to database
        $this->manager->persist($this->product);
        $this->manager->flush();

        // return the save product
        return $this->product;

/*
 * TODO : Missing fields
nutriscore
picture
ingredientPicture
nutritionPicture
$servingSize
$user (from loged in user)
*/

    }

    private function navigate()
    {
        //set follow redirect
        $this->client->followRedirects(true);

        /*
         * TODO SET INFO IN CONFIG FILE
         */
        // create a new crawler
        $this->client->request(
            'POST',
            'https://fr.openfoodfacts.org/cgi/session.pl',
            ['user_id' => 'stock@frogg.fr', 'password' => 'scrapOFF']
        );

        $this->crawler = $this->client->request(
            'GET',
            'https://fr.openfoodfacts.org/cgi/product.pl?type=edit&code=' . $this->product->getBarcode()
        );

    }


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
     * @return null|string
     */
    private function getValue(array $line) : ?string
    {
        if(strstr ($line[0],'_unit')) {
            $node = $this->crawler->filter($line[0] . ' option:selected');
            return $node->count() ? $node->eq(0)->text() : null;
        }else{
            $node = $this->crawler->filter($line[0]);
            return $node->count() ? $node->eq(0)->attr('value') : null;
        }
     }


    /**
     * @param string $value
     * @param array $line
     * @return array|float|int|string
     * @throws ProductTypeException
     */
    private function formatValue(string $value, array $line)
    {
        // link to entity
        if(count($line)===4){

            //get the list of values as string
            $values = array_map('trim',explode(",",$value));

            // get the value as array of object
            $value = $this->getEntityValue($values,$line[3]);
        }

        // Convert to required type
        switch($line[1]){
            case 'array':
            case 'string':
                break;
            case 'int':
                $value=intval($value);
                break;
            case 'float':
                $value=floatval($value);
                break;
            default:
                throw new ProductTypeException('invalid type ' . $line[2]);
        }

        return $value;
    }

    /**
     * @param array $values
     * @param string $entity
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