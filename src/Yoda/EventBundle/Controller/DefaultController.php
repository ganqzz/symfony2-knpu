<?php

namespace Yoda\EventBundle\Controller;

class DefaultController extends Controller
{
    public function indexAction($count, $firstName)
    {
        // parameter ex
        $hoge = $this->container->getParameter('hoge');

        //$em = $this->container->get('doctrine')->getManager();
        $em = $this->getDoctrine()->getManager();
        //$repo = $em->getRepository('Yoda\EventBundle\Entity\Event');
        $repo = $em->getRepository('EventBundle:Event');

        //$event = $this->getEventRepository()->findOneBy(array(
        //    'name' => 'Darth\'s surprise birthday party',
        //));
        $event = $this->getEventRepository()->getRecentlyUpdatedEvents()[0];
        //var_dump($event);
        //die();

        return $this->render(
            'EventBundle:Default:index.html.twig',
            array(
                'name'  => $firstName,
                'count' => $count,
                'event' => $event,
                'hoge'  => $hoge,
            )
        );
    }
}
