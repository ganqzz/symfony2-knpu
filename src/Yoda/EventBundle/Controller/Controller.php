<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\SecurityContext;
use Yoda\EventBundle\Entity\Event;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Yoda\EventBundle\Entity\EventRepository;

class Controller extends BaseController
{
    /**
     * @return EventRepository
     */
    public function getEventRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
    }

    /**
     * @return SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->container->get('security.context');
    }

    public function enforceOwnerSecurity(Event $event)
    {
        $user = $this->getUser();

        if ($user != $event->getOwner()) {
            // if you're using 2.5 or higher
            // throw $this->createAccessDeniedException('You are not the owner!!!');
            throw new AccessDeniedException('You are not the owner!!!');
        }
    }
}
