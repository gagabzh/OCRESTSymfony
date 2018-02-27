<?php
/**
 * Created by PhpStorm.
 * User: bgarnier
 * Date: 27/02/2018
 * Time: 12:29
 */

namespace AppBundle\Repository;

class ArticleRepository extends AbstractRepository
{
    public function search($term, $order = 'asc', $limit = 20, $offset = 0)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.title', $order)
        ;

        if ($term) {
            $qb
                ->where('a.title LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }

        return $this->paginate($qb, $limit, $offset);
    }
}