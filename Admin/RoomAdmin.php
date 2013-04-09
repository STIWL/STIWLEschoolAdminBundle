<?php

namespace Esolving\Eschool\BackendBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class RoomAdmin extends Admin {

    protected $maxPerPage = 10;
    protected $translationDomain = 'EsolvingEschoolRoomBundle';

    public function getTypeByCategoryByLanguage($xcategory, $xlanguage) {
        return $getSex = $this
                ->getConfigurationPool()
                ->getContainer()
                ->get("doctrine")
                ->getRepository("EsolvingEschoolCoreBundle:Type")
                ->findByCategoryByLanguage($xcategory, $xlanguage);
        ;
    }

    public function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->add('roomType', null, array('label' => 'room', 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('headquarterType', null, array('label' => 'headquarter', 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('sectionType', null, array('label' => 'section', 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('status')
        ;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $formMapper
                ->add('roomType', null, array(
                    'group_by' => 'roomType',
                    'choices' => $this->getTypeByCategoryByLanguage("room", $this->getRequest()->getLocale()),
                    'property' => 'languages.values[0].description',
                    'required' => true,
                    'label' => 'room'
                        )
                )
                ->add('headquarterType', null, array(
                    'group_by' => 'headquarterType',
                    'choices' => $this->getTypeByCategoryByLanguage("headquarter", $this->getRequest()->getLocale()),
                    'property' => 'languages.values[0].description',
                    'required' => true,
                    'label' => 'headquarter'
                        )
                )
                ->add('sectionType', null, array(
                    'group_by' => 'sectionType',
                    'choices' => $this->getTypeByCategoryByLanguage("section", $this->getRequest()->getLocale()),
                    'property' => 'languages.values[0].description',
                    'required' => true,
                    'label' => 'section'
                        )
                )
                ->add('status')
        ;
    }

    public function createQuery($context = 'list') {
        $query = parent::createQuery($context);
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $queryRooms = $em->getRepository('EsolvingEschoolRoomBundle:Room')->findAllByLanguageToSonataAdmin($this->getRequest()->getLocale(), $query);
        return $queryRooms;
    }

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('roomType', null, array('template' => 'EsolvingEschoolBackendBundle:List:type.html.twig', 'label' => 'room'))
                ->add('sectionType', null, array('template' => 'EsolvingEschoolBackendBundle:List:type.html.twig', 'label' => 'section'))
                ->add('headquarterType', null, array('template' => 'EsolvingEschoolBackendBundle:List:type.html.twig', 'label' => 'headquarter'))
                ->add('createdAt', null, array('label' => 'date_registered'))
                ->add('updatedAt', null, array('label' => 'date_modificated'))
                ->add('disabledAt', null, array('label' => 'date_disabled'))
                ->add('status', null, array("label" => 'status'))
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'view' => array()
                    )
                ))
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('roomType', null, array("label" => 'room'))
                ->add('headquarterType', null, array("label" => 'headquarter'))
        ;
    }

}