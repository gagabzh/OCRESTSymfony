<?php
/**
 * Created by PhpStorm.
 * User: bgarnier
 * Date: 27/02/2018
 * Time: 16:18
 */

namespace AppBundle\Repository;

class AuthorRepository extends AbstractRepository
{
    public function search($term, $order = 'asc', $limit = 10, $offset = 1)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.fullname', $order)
        ;

        if ($term) {
            $qb
                ->where('a.fullname LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }

        return $this->paginate($qb, $limit, $offset);
    }
}