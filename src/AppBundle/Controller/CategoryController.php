<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Category controller.
 */
class CategoryController extends Controller
{
    /**
     * Finds and displays a category entity.
     *
     * @Route("/category/{slug}/{page}", defaults={"page": 1}, name="category_show")
     * @Method("GET")
     */
    public function showAction($slug, $page)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AppBundle:Category')->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $total_jobs = $em->getRepository('AppBundle:Job')->countActiveJobs($category->getId()); // 1
        $jobs_per_page = $this->container->getParameter('max_jobs_on_category'); // 20
        $last_page = ceil($total_jobs / $jobs_per_page); // 1 / 20 = 2
        $previous_page = $page > 1 ? $page - 1 : 1; // 1
        $next_page = $page < $last_page ? $page + 1 : $last_page; // 2

        $category->setActiveJobs($em->getRepository('AppBundle:Job')->getActiveJobs($category->getId(),
            $jobs_per_page, ($page - 1) * $jobs_per_page));

        $format = $this->getRequest()->getRequestFormat();

        return $this->render('AppBundle:Category:show.'.$format.'.twig', array(
            'category' => $category,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_jobs' => $total_jobs,
            'feedId' => sha1($this->get('router')->generate('category_show',
                array('slug' =>  $category->getSlug(), '_format' => 'atom'), true)),
        ));
    }
}