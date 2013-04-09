<?php

namespace Esolving\Eschool\BackendBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Esolving\Eschool\DisplayBundle\Entity\Setting;
use Esolving\Eschool\CoreBundle\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SettingAdmin extends Admin {

    protected $maxPerPage = 30;
    protected $translationDomain = 'EsolvingEschoolBackendBundle';

    public function getSettings($xlanguage, $xcurrent_setting_id) {
        $getSettings = $this
                ->getConfigurationPool()
                ->getContainer()
                ->get("doctrine")
                ->getRepository('EsolvingEschoolCoreBundle:Type')
                ->findAllSettingsByLanguageNoDuplicated($xlanguage, $xcurrent_setting_id);
        ;
        return $getSettings;
    }

    public function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->add('settingType', null, array("label" => 'setting'))
                ->add('value', null, array("label" => 'value'))
                ->add('status', null, array("label" => 'status'))
        ;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $id = $this->getRequest()->get($this->getIdParameter());
        $formMapper
                ->with("general")
                ->add('settingType', null, array(
                    'group_by' => 'settingType',
                    'choices' => $this->getSettings($this->getRequest()->getLocale(), $id),
                    'property' => 'languages.values[0].description',
                    'required' => true,
                    'label' => 'setting'
                ))
                ->add('name', null, array('label' => 'name'))
                ->add('value', null, array("label" => 'value'))
                ->add('status', null, array("label" => 'status'))
                ->end()
        ;
    }

    public function createQuery($context = 'list') {
        $query = parent::createQuery($context);
        $doctrine = $this->getConfigurationPool()->getContainer()->get('doctrine');
        $querySettings = $doctrine->getManager()->getRepository('EsolvingEschoolCoreBundle:Setting')->findAllByLanguageToSonataAdmin($this->getRequest()->getLocale(), $query);
        return $querySettings;
    }

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('settingType', null, array(
                    "label" => 'setting',
                    'template' => 'EsolvingEschoolBackendBundle::type.html.twig',
                ))
                ->add('value', null, array("label" => 'value'))
                ->add('languages', null, array("label" => 'languages'))
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
                ->add('settingType', null, array("label" => 'setting'))
                ->add('value', null, array("label" => 'value'))
                ->add('status', null, array("label" => 'status'))
        ;
    }

}