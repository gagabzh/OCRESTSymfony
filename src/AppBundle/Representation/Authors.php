<?php
/**
 * Created by PhpStorm.
 * User: bgarnier
 * Date: 27/02/2018
 * Time: 16:13
 */

namespace AppBundle\Representation;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JMS\Serializer\Annotation\Type;

class Authors
{
    /**
     * @Type("array<AppBundle\Entity\Author>")
     */
    public $data;
    public $meta;

    public function __construct(Paginator $data)
    {
        $this->data = $data;
        $this->addMeta('total_items', count($data));
    }

    public function addMeta($name, $value)
    {
        if (isset($this->meta[$name])) {
            throw new \LogicException(sprintf('This meta already exists. You are trying to override this meta, use the setMeta method instead for the %s meta.', $name));
        }

        $this->setMeta($name, $value);
    }

    public function setMeta($name, $value)
    {
        $this->meta[$name] = $value;
    }
}