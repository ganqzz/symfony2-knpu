<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Yoda\EventBundle\Entity\Event;
use Yoda\EventBundle\Form\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{

    /**
     * Lists all Event entities.
     * @Route("/", name="event")
     * @Template()
     */
    public function indexAction()
    {
        ////$em = $this->getDoctrine()->getManager();
        ////$entities = $em->getRepository('EventBundle:Event')->findAll();
        ////$entities = $this->getEventRepository()->findAll();
        //$entities = $this->getEventRepository()->getUpcomingEvents();
        //
        //return array(
        //    'entities' => $entities,
        //);
        return array();
    }

    /**
     * Rendering partials
     * @param null $max
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function _upcomingEventsAction($max = null)
    {
        //$em = $this->getDoctrine()->getManager();
        //$events = $em->getRepository('EventBundle:Event')->getUpcomingEvents($max);
        $events = $this->getEventRepository()->getUpcomingEvents($max);

        return $this->render('EventBundle:Event:_upcomingEvents.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * Creates a new Event entity.
     *
     */
    public function createAction(Request $request)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->getUser();
            // this works!
            // this is setting the owning side of the relationship
            $entity->setOwner($user);

            // this does nothing
            // if we *only* had this part, the relationship would not save
            // $events = $this->getUser()->getEvents();
            // $events[] = $entity;
            // $this->getUser()->setEvents($events);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show',
                array('slug' => $entity->getSlug())));
        }

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Event entity.
     *
     */
    public function newAction()
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $entity = new Event();
        $form = $this->createCreateForm($entity);

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
     *
     */
    public function showAction($slug)
    {
        //$em = $this->getDoctrine()->getManager();
        //$entity = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));
        $entity = $this->getEventRepository()->findOneBy(array('slug' => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('EventBundle:Event:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     */
    public function editAction($id)
    {
        $this->enforceUserSecurity();

        //$em = $this->getDoctrine()->getManager();
        //$entity = $em->getRepository('EventBundle:Event')->find($id);
        $entity = $this->getEventRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->enforceOwnerSecurity($entity);

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Event entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->enforceUserSecurity();

        $em = $this->getDoctrine()->getManager();
        //$entity = $em->getRepository('EventBundle:Event')->find($id);
        $entity = $this->getEventRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->enforceOwnerSecurity($entity);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('event_edit', array('id' => $id)));
        }

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Event entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $this->enforceUserSecurity();

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->getEventRepository()->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            $this->enforceOwnerSecurity($entity);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }

    /**
     * Creates a form to delete a Event entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function attendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $event \Yoda\EventBundle\Entity\Event */
        //$event = $em->getRepository('EventBundle:Event')->find($id);
        $event = $this->getEventRepository()->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id ' . $id);
        }

        if (!$event->hasAttendee($this->getUser())) {
            $event->getAttendees()->add($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unattendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $event \Yoda\EventBundle\Entity\Event */
        //$event = $em->getRepository('EventBundle:Event')->find($id);
        $event = $this->getEventRepository()->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id ' . $id);
        }

        if ($event->hasAttendee($this->getUser())) {
            $event->getAttendees()->removeElement($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    /**
     * @param Event $event
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createAttendingResponse(Event $event, $format)
    {
        if ($format == 'json') {
            $data = [
                'attending' => $event->hasAttendee($this->getUser())
            ];

            $response = new JsonResponse($data);

            return $response;
        }

        return $this->redirect(
            $this->generateUrl('event_show', ['slug' => $event->getSlug()])
        );
    }

    private function enforceUserSecurity($role = 'ROLE_USER')
    {
        if (!$this->getSecurityContext()->isGranted($role)) {
            // in Symfony 2.5 or higher
            // throw $this->createAccessDeniedException('message!');
            throw new AccessDeniedException('Need ' . $role);
        }
    }
}
