<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use AppBundle\Representation\Articles;
use AppBundle\Exception\ResourceValidationException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Request;



class ArticleController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(populateDefaultVars=false)
     */
    public function showAction(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post(
     *    path = "/articles",
     *    name = "app_article_create"
     * )
     * @Rest\View(StatusCode = 201,populateDefaultVars=false)
     * @ParamConverter(
     *     "article",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     */
    public function createAction(Article $article, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($article);
        $em->flush();

        return $this->view($article, Response::HTTP_CREATED, ['Location' => $this->generateUrl('app_article_show', ['id' => $article->getId()])]);
    }
    /**
     * @Rest\Put(
     *    path = "/articles/{id}",
     *    name = "app_article_update",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200,populateDefaultVars=false)
     *
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $new = $em->getRepository('AppBundle:Article')->find($request->get('id'));
        if (null === $new) {
            throw new ResourceValidationException("L'annonce d'id ".$request->get('id')." n'existe pas.");
        }

        $form = $this->createForm(ArticleType::class, $new);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            // l'entité vient de la base, donc le merge n'est pas nécessaire.
            // il est utilisé juste par soucis de clarté
            $em->merge($new);
            $em->flush();
            return $this->view($new, Response::HTTP_OK, ['Location' => $this->generateUrl('app_article_show', ['id' => $new->getId()])]);;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Patch(
     *    path = "/articles/{id}",
     *    name = "app_article_patch",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200,populateDefaultVars=false)
     *
     */
    public function patchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $new = $em->getRepository('AppBundle:Article')->find($request->get('id'));
        if (null === $new) {
            throw new ResourceValidationException("L'annonce d'id ".$request->get('id')." n'existe pas.");
        }

        $form = $this->createForm(ArticleType::class, $new);
        $form->submit($request->request->all(),false);

        if ($form->isValid()) {
            // l'entité vient de la base, donc le merge n'est pas nécessaire.
            // il est utilisé juste par soucis de clarté
            $em->merge($new);
            $em->flush();
            return $this->view($new, Response::HTTP_OK, ['Location' => $this->generateUrl('app_article_show', ['id' => $new->getId()])]);;
        } else {
            return $form;
        }

    }

    /**
     * @Rest\Delete(
     *    path = "/articles/{id}",
     *    name = "app_article_delete",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT,populateDefaultVars=false)
     *
     */
    public function deleteAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $new = $em->getRepository('AppBundle:Article')->find($article->getId());
        if (null === $new) {
            throw new ResourceValidationException("L'annonce d'id ".$article->getId()." n'existe pas.");
        }

        $em->remove($new);
        $em->flush();
    }

    /**
     * @Rest\Get("/articles", name="app_article_list")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="3",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="5",
     *     description="The pagination offset"
     * )
     * @Rest\View(populateDefaultVars=false)
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Articles($pager);
    }


}
