<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function getWithJobs()
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c FROM AppBundle:Category c LEFT JOIN c.jobs j WHERE j.expiresAt > :date AND j.isActivated = :activated'
        )->setParameter('date', date('Y-m-d H:i:s', time()))->setParameter('activated', 1);

        return $query->getResult();
    }
}
