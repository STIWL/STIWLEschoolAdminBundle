<?php

namespace Esolving\Eschool\BackendBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Esolving\Eschool\CoreBundle\Entity\TypeLanguage;

class TypeAdmin extends Admin {

    protected $maxPerPage = 30;
    protected $translationDomain = 'EsolvingEschoolBackendBundle';

//    public function postPersist($object) {
//        parent::postPersist($object);
//        $typeId = $this
//                ->getConfigurationPool()
//                ->getContainer()
//                ->get("doctrine")
//                ->getRepository('EsolvingEschoolCoreBundle:Type')
//                ->find($object->getId())
//        ;
//        $languages = $this->getConfigurationPool()->getContainer()->get("display")->languages();
//        foreach ($languages as $languagesV) {
//            $language = new Language();
//            $language->setDescription($object->getName());
//            $language->setLanguage($languagesV->getFilename());
//            $language->setType($typeId);
//            $em = $this
//                    ->getConfigurationPool()
//                    ->getContainer()
//                    ->get("doctrine")
//                    ->getEntityManager()
//            ;
//            $em->persist($language);
//        }
//        $em->flush();
//    }

    public function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->add('languages', null, array("label" => 'languages', 'template' => 'EsolvingEschoolBackendBundle:Show:languages.html.twig'))
                ->add('name', null, array("label" => 'name'))
                ->add('status', null, array("label" => 'status'))
        ;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $formMapper
                ->with("general")
                ->add('category', null, array("label" => 'category'))
                ->add('name', null, array("label" => 'name'))
                ->add('status', null, array("label" => 'status'))
                ->with("languages")
                ->add('languages', 'sonata_type_collection', array(
                    'required' => true, 'by_reference' => false, 'label' => 'languages'
                        ), array(
                    'edit' => 'inline',
                    'inline' => 'table'
                ))
                ->end()
        ;
    }

    public function createQuery($context = 'list') {
        $query = parent::createQuery($context);
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $queryTypes = $em->getRepository('EsolvingEschoolCoreBundle:Type')->findAllByLanguageToSonataAdmin($this->getRequest()->getLocale(), $query);
        return $queryTypes;
    }

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('languages', null, array("label" => 'languages', 'template' => 'EsolvingEschoolBackendBundle:List:languages.html.twig'))
                ->addIdentifier('name', null, array("label" => 'name'))
                ->add('status', null, array("label" => 'status'))
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
                ->add('category', null, array("label" => 'category'))
                ->add('name', null, array("label" => 'name'))
                ->add('status', null, array("label" => 'status'))
        ;
    }

}