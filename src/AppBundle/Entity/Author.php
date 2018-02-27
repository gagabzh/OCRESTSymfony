<?php
/**
 * Created by PhpStorm.
 * User: bgarnier
 * Date: 27/02/2018
 * Time: 09:54
 */
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
 * @ExclusionPolicy("all")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_author_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *       )
 * )
 */
class Author
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Expose
     * @Assert\NotBlank(groups={"Create"})
     */
    private $fullname;

    /**
     * @ORM\Column(type="text")
     * @Expose
     */
    private $biography;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="author", cascade={"persist"})
     */
    private $articles;

    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getBiography()
    {
        return $this->biography;
    }

    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    public function getArticles()
    {
        return $this->articles;
    }
}