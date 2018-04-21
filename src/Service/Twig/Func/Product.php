<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Twig\Func;

use App\Common\Traits\Product\FolderTrait;
use App\Entity\StockProducts;
use App\Service\Twig\AbstractTwigExtension;
use App\SiteConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * @author Frogg <admin@frogg.fr>
 */
class Product extends AbstractTwigExtension
{

    use FolderTrait;


    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Request
     */
    private $request;

    /**
     * Product constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack)
    {
        $this->manager = $manager;
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @param string $barcode
     * @param string $image |null
     *
     * @return string
     */
    public function getProductImage(string $barcode, ?string $image): string
    {
        return SiteConfig::UPLOADPATH . $this->getFolder($barcode) . $image;
    }


    /**
     * @param Iterable  $object
     *
     * @return string
     */
    public function getFormatedList(Iterable $object)
    {
        $arrayObject = $object->toArray();

        return implode(', ',array_map(function ($value) {return $value->getName(); },$arrayObject,array_keys($arrayObject)));
    }

    /**
     * @param int $productId
     *
     * @return string
     */
    public function getExpires(int $productId, int $stockId): string
    {
        //init
        $result = '';
        $selected = 'selected';

        // get data from database
        $stockProducts = $this->manager->getRepository(StockProducts::class)->findExpires($productId, $stockId);

        /**
         * @var StockProducts $product
         */
        foreach ($stockProducts as $product) {
            //Get expiration date
            $expireDate = $product->getDateExpire();

            //date as string for display
            $dateString = "no expire data set";

            // create the related option
            if (null !== $expireDate) {
                $dateString = $expireDate->format(SiteConfig::DATELOCALE[$this->request->getLocale()]);
            }

            //create the option
            $result .= '<option value="' . $product->getId() . '" ' . $selected . '>' . $dateString . '</option>';

            //reset selected for other expiration dates
            $selected = '';
        }

        return $result;
    }

    /**
     * @param \App\Entity\Product $product
     *
     * @return string
     */
    public function getNutritionalInfo(\App\Entity\Product $product)
    {
        $html = '<div>';

        $html .= $this->getNutritionLine('Fat', $product);
        $html .= $this->getNutritionLine('SaturedFat', $product);
        $html .= $this->getNutritionLine('Sugar', $product);
        $html .= $this->getNutritionLine('Salt', $product);

        return $html.'</div>';
    }


    /**
     * @param string $type
     * @param Product $product
     *
     * @return string
     */
    private function getNutritionLine(string $type, \App\Entity\Product $product)
    {
        //prepare dynamic method name call
        $level = 'getLevel' . $type;
        $unit = 'getLevel' . $type . 'Unit';

        //get dynamically datas
        $levelData = $product->$level();
        $unitData = $product->$unit();

        //set default value
        if(null===$levelData){
            $levelData = 0;
        }
        if(null===$unitData){
            $unitData = 'g';
        }

        // get values from product info
        $value = $levelData / SiteConfig::WEIGHTUNIT[$unitData];
        $grade = 1;

        /**
         * @see https://fr.openfoodfacts.org/reperes-nutritionnels
         * Lipides    jusqu'à 3g    de 3g à 20g    plus de 20g
         * Acides gras saturés    jusqu'à 1,5g    de 1,5g à 5g    plus de 5g
         * Sucres    jusqu'à 5g    de 5g à 12,5g    plus de 12,5g
         * Sel    jusqu'à 0,3g    de 0,3g à 1,5g    plus de 1,5g
         */

        // get the grade from datas
        switch ($type) {
            case 'Fat':
                if ($value <= 3) {
                    $grade = 0;
                }
                if ($value > 20) {
                    $grade = 2;
                }
                break;
            case 'SaturedFat':
                if ($value <= 1.5) {
                    $grade = 0;
                }
                if ($value > 5) {
                    $grade = 2;
                }
                break;
            case 'Sugar':
                if ($value <= 5) {
                    $grade = 0;
                }
                if ($value > 12.5) {
                    $grade = 2;
                }
                break;
            case 'Salt':
                if ($value <= 0.3) {
                    $grade = 0;
                }
                if ($value > 1.5) {
                    $grade = 2;
                }
                break;
        }



        return '<div><a class="btn-floating '
            . SiteConfig::PRODUCTGRADE[$grade]['color']
            . ' waves-effect waves-light mini"></a>'
            . $levelData . ' ' . $unitData
            . '  (TODO TRAD)' . $type . ' '
            . SiteConfig::PRODUCTGRADE[$grade]['text']
            . '</div>';
    }

}