<?php

namespace Esolving\Eschool\BackendBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class LanguageAdminController extends Controller {

//    public function showAction($id = null) {
//        $id = $this->get('request')->get($this->admin->getIdParameter());
//
//        $object = $this->admin->getObject($id);
//        $em = $this->getDoctrine()->getManager();
//        
//        if (!$object) {
//            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
//        }
//
//        if (false === $this->admin->isGranted('VIEW', $object)) {
//            throw new AccessDeniedException();
//        }
//        $user = $em->getRepository('EsolvingEschoolUserBundle:User')->findOneByUserIdByLanguage($object->getId(), $this->getRequest()->getLocale());
//
//        $this->admin->setSubject($object);
//
//        return $this->render($this->admin->getTemplate('show'), array(
//                    'action' => 'show',
//                    'object' => $user,
//                    'elements' => $this->admin->getShow(),
//        ));
//    }

}

?>
