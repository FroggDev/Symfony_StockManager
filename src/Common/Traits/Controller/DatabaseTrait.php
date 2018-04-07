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

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Trait DatabaseTrait
 *
 * usage :
 * -------
 * use DatabaseTrait;
 * $this->save($entity);
 */
trait DatabaseTrait
{
    /**
     * @param object $entity the entity to save
     */
    private function save(object $entity) : void
    {
        // insert into database
        $eManager = $this->getDoctrine()->getManager();
        $eManager->persist($entity);
        $eManager->flush();
    }
}
