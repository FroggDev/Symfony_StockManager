<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Common\Traits\Controller;

use Symfony\Component\Form\FormErrorIterator;

/**
 * @author Frogg <admin@frogg.fr>
 */
trait FormCheckTrait
{
    /**
     * @param FormErrorIterator $errors
     * @return bool
     */
    private function isOk(FormErrorIterator $errors): bool
    {
        if (count($errors) > 0) {
            //Add error message
            foreach ($errors as $key => $error) {
                $this->addFlash('error', $error->getMessage());

                return false;
            }

            return true;
        }
    }
}
