<?php
/**
 * Created by PhpStorm.
 * User: bgarnier
 * Date: 27/02/2018
 * Time: 09:56
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use AppBundle\Representation\Authors;
use AppBundle\Exception\ResourceValidationException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/authors/{id}",
     *     name = "app_author_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(populateDefaultVars=false)
     */
    public function showAction(Author $item)
    {
        return $item;
    }

    /**
     * @Rest\Get("/authors", name="app_authors_list")
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
     *     default="10",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * @Rest\View(populateDefaultVars=false)
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Author')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order')
        );
        return new Authors($pager);
    }

    /**
     * @Rest\Post(
     *    path = "/authors",
     *    name = "app_article_create"
     * )
     * @Rest\View(StatusCode = 201,populateDefaultVars=false)
     * @ParamConverter(
     *     "item",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     */
    public function createAction(Author $item, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($item);
        $em->flush();

        return $this->view($item, Response::HTTP_CREATED, ['Location' => $this->generateUrl('app_article_show', ['id' => $item->getId()])]);
    }

    /**
     * @Rest\Put(
     *    path = "/authors/{id}",
     *    name = "app_author_update",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200,populateDefaultVars=false)
     *
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $new = $em->getRepository('AppBundle:Author')->find($request->get('id'));
        if (null === $new) {
            throw new ResourceValidationException("L'annonce d'id ".$request->get('id')." n'existe pas.");
        }

        $form = $this->createForm(AuthorType::class, $new);
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
     *    path = "/authors/{id}",
     *    name = "app_aauthor_patch",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200,populateDefaultVars=false)
     *
     */
    public function patchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $new = $em->getRepository('AppBundle:Author')->find($request->get('id'));
        if (null === $new) {
            throw new ResourceValidationException("L'annonce d'id ".$request->get('id')." n'existe pas.");
        }

        $form = $this->createForm(AuthorType::class, $new);
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
     *    path = "/authors/{id}",
     *    name = "app_author_delete",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT,populateDefaultVars=false)
     *
     */
    public function deleteAction(Author $item)
    {
        $em = $this->getDoctrine()->getManager();
        $new = $em->getRepository('AppBundle:Author')->find($item->getId());
        if (null === $new) {
            throw new ResourceValidationException("L'annonce d'id ".$item->getId()." n'existe pas.");
        }

        $em->remove($new);
        $em->flush();
    }
}