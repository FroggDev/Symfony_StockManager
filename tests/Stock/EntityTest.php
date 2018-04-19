<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Stock;

use App\Entity\Country;
use App\Entity\Product;
use App\Entity\Product\Additive;
use App\Entity\Product\Alergy;
use App\Entity\Product\Brand;
use App\Entity\Product\Category;
use App\Entity\Product\Certification;
use App\Entity\Product\Origin;
use App\Entity\Product\Packaging;
use App\Entity\Product\Place;
use App\Entity\Product\Trace;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class EntityTest extends WebTestCase
{
    /*######################
     # GETTER/SETTER TESTS #
     ######################*/

    public function testGetterAndSetter() : void
    {
        // INIT
        //-----

        $country = new Country();

        // TEST
        //-----

        $this->assertSame(1,
            $country->setId(1)->getId()
        );

        $this->assertSame(1,
            $country->setCode(1)->getCode()
        );

        $this->assertSame('random',
            $country->setAlpha2('random')->getAlpha2()
        );

        $this->assertSame('random',
            $country->setAlpha3('random')->getAlpha3()
        );


        $this->assertSame('random',
            $country->setName('random')->getName()
        );

        /*
         * FACTORISATION :
        $trace = new Trace();
        $reflect = new ReflectionClass($trace);
        $props   = $reflect->getProperties();
        dump($props);
        exit();
        */

        // INIT
        //-----

        $trace = new Trace();

        // TEST
        //-----

        $this->assertSame(1,
            $trace->setId(1)->getId()
        );

        $this->assertSame('random',
            $trace->setName('random')->getName()
        );


        // INIT
        //-----

        $place = new Place();

        // TEST
        //-----

        $this->assertSame(1,
            $place->setId(1)->getId()
        );

        $this->assertSame('random',
            $place->setName('random')->getName()
        );

        // INIT
        //-----

        $alergy = new Alergy();

        // TEST
        //-----

        $this->assertSame(1,
            $alergy->setId(1)->getId()
        );

        $this->assertSame('random',
            $alergy->setName('random')->getName()
        );


        // INIT
        //-----

        $additive = new Additive();

        // TEST
        //-----

        $this->assertSame(1,
            $additive->setId(1)->getId()
        );

        $this->assertSame('random',
            $additive->setName('random')->getName()
        );


        // INIT
        //-----

        $brand = new Brand();

        // TEST
        //-----

        $this->assertSame(1,
            $brand->setId(1)->getId()
        );

        $this->assertSame('random',
            $brand->setName('random')->getName()
        );


        // INIT
        //-----

        $category = new Category();

        // TEST
        //-----

        $this->assertSame(1,
            $category->setId(1)->getId()
        );

        $this->assertSame('random',
            $category->setName('random')->getName()
        );



        // INIT
        //-----

        $certification = new Certification();

        // TEST
        //-----

        $this->assertSame(1,
            $certification->setId(1)->getId()
        );

        $this->assertSame('random',
            $certification->setName('random')->getName()
        );


        // INIT
        //-----

        $origin = new Origin();

        // TEST
        //-----

        $this->assertSame(1,
            $origin->setId(1)->getId()
        );

        $this->assertSame('random',
            $origin->setName('random')->getName()
        );


        // INIT
        //-----

        $packing = new Packaging();

        // TEST
        //-----

        $this->assertSame(1,
            $packing->setId(1)->getId()
        );

        $this->assertSame('random',
            $packing->setName('random')->getName()
        );

        // INIT
        //-----

        $place = new Place();

        // TEST
        //-----

        $this->assertSame(1,
            $place->setId(1)->getId()
        );

        $this->assertSame('random',
            $place->setName('random')->getName()
        );


        // INIT
        //-----

        $product = new Product();

        // TEST
        //-----

        $this->assertSame(1,
            $product->setId(1)->getId()
        );

        $this->assertSame(111,
            $product->setBarcode(111)->getBarcode()
        );

        $this->assertSame('random',
            $product->setEmbCode('random')->getEmbCode()
        );

        $this->assertSame('random',
            $product->setName('random')->getName()
        );

        $this->assertSame('random',
            $product->setCommonName('random')->getCommonName()
        );

        $date= new \DateTime();
        $this->assertSame($date,
            $product->setDateCreation($date)->getDateCreation()
        );

        $this->assertSame('random',
            $product->setQuantity('random')->getQuantity()
        );

        $this->assertSame('random',
            $product->setPicture('random')->getPicture()
        );

        $this->assertSame('random',
            $product->setProducerPage('random')->getProducerPage()
        );

        $user = new User();
        $this->assertSame($user,
            $product->setUser($user)->getUser()
        );

        $this->assertSame([$country],
            $product->setCountries([$country])->getCountries()
        );

        $this->assertSame([$certification],
            $product->setCertifications([$certification])->getCertifications()
        );

        $this->assertSame([$origin],
            $product->setOrigins([$origin])->getOrigins()
        );

        $this->assertSame([$place],
            $product->setPlaces([$place])->getPlaces()
        );

        $this->assertSame([$packing],
            $product->setPackagings([$packing])->getPackagings()
        );

        $this->assertSame([$category],
            $product->setCategories([$category])->getCategories()
        );

        $this->assertSame([$brand],
            $product->setBrands([$brand])->getBrands()
        );

        $this->assertSame([$alergy],
            $product->setAlergies([$alergy])->getAlergies()
        );

        $this->assertSame([$trace],
            $product->setTraces([$trace])->getTraces()
        );

        $this->assertSame([$additive],
            $product->setAdditives([$additive])->getAdditives()
        );

        $this->assertSame('random',
            $product->setIngredientPicture('random')->getIngredientPicture()
        );

        $this->assertSame('random',
            $product->setIngredients('random')->getIngredients()
        );

        $this->assertSame('random',
            $product->setNutritionPicture('random')->getNutritionPicture()
        );

        $this->assertSame('random',
            $product->setNutriscore('random')->getNutriscore()
        );

        $this->assertSame('random',
            $product->setServingSize('random')->getServingSize()
        );

        $this->assertSame(1.0,
            $product->setEnergy(1)->getEnergy()
        );

        $this->assertSame('random',
            $product->setEnergyUnit('random')->getEnergyUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelFat(1)->getLevelFat()
        );

        $this->assertSame('random',
            $product->setLevelFatUnit('random')->getLevelFatUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelSaturedFat(1)->getLevelFat()
        );

        $this->assertSame('random',
            $product->setLevelSaturedFatUnit('random')->getLevelSaturedFatUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelCarbohydrate(1)->getLevelCarbohydrate()
        );

        $this->assertSame('random',
            $product->setLevelCarbohydrateUnit('random')->getLevelCarbohydrateUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelSugar(1)->getLevelSugar()
        );

        $this->assertSame('random',
            $product->setLevelSugarUnit('random')->getLevelSugarUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelDietaryFiber(1)->getLevelDietaryFiber()
        );

        $this->assertSame('random',
            $product->setLevelDietaryFiberUnit('random')->getLevelDietaryFiberUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelProteins(1)->getLevelProteins()
        );

        $this->assertSame('random',
            $product->setLevelProteinsUnit('random')->getLevelProteinsUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelSalt(1)->getLevelSalt()
        );

        $this->assertSame('random',
            $product->setLevelSaltUnit('random')->getLevelSaltUnit()
        );


        $this->assertSame(1.0,
            $product->setLevelSodium(1)->getLevelSodium()
        );

        $this->assertSame('random',
            $product->setLevelSodiumUnit('random')->getLevelSodiumUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelAlcohol(1)->getLevelAlcohol()
        );

        $this->assertSame(1.0,
            $product->setLevelSilica(1)->getLevelSilica()
        );

        $this->assertSame('random',
            $product->setLevelSilicaUnit('random')->getLevelSilicaUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelBicarbonate(1)->getLevelBicarbonate()
        );

        $this->assertSame('random',
            $product->setLevelBicarbonateUnit('random')->getLevelBicarbonateUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelPotassium(1)->getLevelPotassium()
        );

        $this->assertSame('random',
            $product->setLevelPotassiumUnit('random')->getLevelPotassiumUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelChloride(1)->getLevelChloride()
        );

        $this->assertSame('random',
            $product->setLevelChlorideUnit('random')->getLevelChlorideUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelCalcium(1)->getLevelCalcium()
        );

        $this->assertSame('random',
            $product->setLevelCalciumUnit('random')->getLevelCalciumUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelMagnesium(1)->getLevelMagnesium()
        );

        $this->assertSame('random',
            $product->setLevelMagnesiumUnit('random')->getLevelMagnesiumUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelNitrates(1)->getLevelNitrates()
        );

        $this->assertSame('random',
            $product->setLevelNitratesUnit('random')->getLevelNitratesUnit()
        );

        $this->assertSame(1.0,
            $product->setLevelSulfates(1)->getLevelSulfates()
        );

        $this->assertSame('random',
            $product->setLevelSulfatesUnit('random')->getLevelSulfatesUnit()
        );


        $this->assertSame(1.0,
            $product->setFootprint(1)->getFootprint()
        );

        $this->assertSame('random',
            $product->setFootprintUnit('random')->getFootprintUnit()
        );
    }
}