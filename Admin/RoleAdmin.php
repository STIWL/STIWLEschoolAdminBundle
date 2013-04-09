<?php

namespace Esolving\Eschool\BackendBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class RoleAdmin extends Admin {

    protected $maxPerPage = 10;
    protected $translationDomain = 'EsolvingEschoolUserBundle';

    public function getTypeRole($currentRoleId = null) {
        $em = $this->getConfigurationPool()
                        ->getContainer()
                        ->get("doctrine")->getManager();
        $roles = $em->getRepository('EsolvingEschoolCoreBundle:Type')->findAllRolesByLanguageNoDuplicated($this->getRequest()->getLocale(), $currentRoleId);
        return $roles;
    }

    public function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->add('roleType', null, array("label" => "role", 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
//                ->add('user', null, array("label" => "user"))
        ;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $role = $this->getSubject();
        $formMapper
                ->with("general")
                ->add('roleType', null, array(
                    'choices' => $this->getTypeRole($role->getId()),
                    'property' => 'languages.values[0].description',
                    'required' => true,
                    'label' => 'role'
                ))
                ->end()
        ;
    }

    public function createQuery($context = 'list') {
        $query = $this->getModelManager()->createQuery($this->getClass());
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $queryRoles = $em->getRepository('EsolvingEschoolUserBundle:Role')->findAllByLanguageToSonataAdmin($this->getRequest()->getLocale(), $query);
        return $queryRoles;
    }

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('roleType', null, array("label" => 'role', 'template' => 'EsolvingEschoolBackendBundle:List:type.html.twig'))
//                ->add('users', null, array("label" => 'user'))
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'view' => array()
                    ),
                    "label" => 'actions'
                ))
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('roleType', null, array("label" => 'role'))
//                ->add('users', null, array("label" => 'user'))
        ;
    }

}