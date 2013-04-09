<?php

namespace Esolving\Eschool\BackendBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ScheduleAdmin extends Admin {

    protected $maxPerPage = 10;
    protected $translationDomain = 'EsolvingEschoolRoomBundle';

    public function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->add('room', null, array('label' => 'room'))
                ->add('teacher', null, array('label' => 'teacher'))
                ->add('timeStart', null, array('label' => 'time_start'))
                ->add('timeEnd', null, array('label' => 'time_end'))
                ->add('status', null, array('label' => 'status'))
        ;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine')->getManager();
        $serviceCore = $container->get('esolving_eschool_core');
        $language = $this->getRequest()->getLocale();
        $headquarter = $serviceCore->getHeadquarter();
        $section = $serviceCore->getSection();
        $headquarterLanguages = $headquarter->getLanguages();
        $sectionLanguages = $section->getLanguages();
        $formMapper
                ->add('room', null, array(
                    'label' => 'room',
                    'choices' => $this->getRoomByLanguage($language),
                    'property' => 'roomType.languages.values[0].description'
                ))
                ->add('teacher', null, array('label' => 'teacher'))
                ->add('timeStart', null, array('label' => 'time_start'))
                ->add('timeEnd', null, array('label' => 'time_end'))
                ->add('status', null, array('label' => 'status'))
                ->setHelps(array(
                    'room' => $headquarterLanguages[0]->getDescription() . ' ' . $sectionLanguages[0]->getDescription()
                ))
        ;
    }

//    public function createQuery($context = 'list') {
//        $query = parent::createQuery($context);
//        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
//        $queryRooms = $em->getRepository('EsolvingEschoolRoomBundle:Room')->findAllByLanguageToSonataAdmin($this->getRequest()->getLocale(), $query);
//        return $queryRooms;
//    }

    private function getRoomByLanguage($language) {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $rooms = $em->getRepository('EsolvingEschoolRoomBundle:Room')->findAllByLanguage($language);
        return $rooms;
    }

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('room', null, array('label' => 'room'))
                ->addIdentifier('teacher', null, array('label' => 'teacher'))
                ->add('timeStart', null, array('label' => 'timeStart'))
                ->add('timeEnd', null, array('label' => 'timeEnd'))
                ->add('status', null, array('label' => 'status'))
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
                ->add('room', null, array("label" => 'room'))
                ->add('teacher', null, array("label" => 'teacher'))
        ;
    }

}