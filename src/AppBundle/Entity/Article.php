<?php
/**
 * Created by PhpStorm.
 * User: bgarnier
 * Date: 26/02/2018
 * Time: 16:35
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 * @ExclusionPolicy("all")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_article_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *       )
 * )
 * @Hateoas\Relation(
 *     "author",
 *     embedded = @Hateoas\Embedded("expr(object.getAuthor())")
 * )
 */
class Article
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Expose
     * @Assert\NotBlank(groups={"Create"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Expose
     * @Assert\NotBlank(groups={"Create"})
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="Author", cascade={"all"}, fetch="EAGER")
     */
    private $author;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }
}