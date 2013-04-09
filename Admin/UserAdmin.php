<?php

namespace Esolving\Eschool\BackendBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Esolving\Eschool\UserBundle\Validator\Constraints\IsFather;
use Esolving\Eschool\UserBundle\Validator\Constraints\IsStudent;
use Esolving\Eschool\UserBundle\Validator\Constraints\IsTeacher;
use Esolving\Eschool\UserBundle\Validator\Constraints\IsStudentRole;
use Symfony\Component\Validator\Constraints\Count;
use Esolving\Eschool\UserBundle\Validator\Constraints\EmailNoRepeat;
use Esolving\Eschool\UserBundle\Entity\Student;
use Esolving\Eschool\UserBundle\Entity\Teacher;
use Esolving\Eschool\UserBundle\Entity\Father;

//use Sonata\AdminBundle\Admin\AdminExtensionInterface;

class UserAdmin extends Admin {

    protected $maxPerPage = 10;
//  Default load messages translations.
//    protected $translationDomain = 'messages';
    protected $translationDomain = 'EsolvingEschoolUserBundle';
    protected $options;

    public function getTypeByCategoryByLanguage($xcategory, $xlanguage) {
        return $getSex = $this
                ->getConfigurationPool()
                ->getContainer()
                ->get("doctrine")
                ->getRepository("EsolvingEschoolCoreBundle:Type")
                ->findByCategoryByLanguage($xcategory, $xlanguage);
        ;
    }

    public function getAllRoleByLanguage($xlanguage) {
        return $getSex = $this
                ->getConfigurationPool()
                ->getContainer()
                ->get('doctrine')
                ->getRepository("EsolvingEschoolUserBundle:Role")
                ->findAllByLanguage($xlanguage);
        ;
    }

    public function getFathers($xuserId = null) {
        $getFathers = $this
                ->getConfigurationPool()->getContainer()
                ->get("doctrine")
                ->getRepository("EsolvingEschoolUserBundle:Father")
                ->findAllExceptSelf($xuserId);
        ;
        return $getFathers;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $user = $this->getSubject();
        $userId = ($user->getId()) ? $user->getId() : null;
        $this->options = array(
            'userId' => $userId
        );

        if (in_array('ROLE_FATHER', $user->getRoles())) {
            $fathers = $this->getFathers($user->getId());
        } else {
            $fathers = $this->getFathers();
        }

        $student = $this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository('EsolvingEschoolUserBundle:Student')->findOneBy(array('user' => $userId));

        if ($student) {
            if (count($student->getFathers()) > 0) {
                foreach ($student->getFathers() as $fatherV) {
                    $user->getFathers()->add($fatherV);
                }
            }
        }

        $formMapper
                ->with('role')
                ->add('rolesAccess', null, array(
                    'constraints' => array(new IsFather(array('userId' => $this->options['userId'])), new IsStudent(array('userId' => $this->options['userId'])), new IsTeacher(array('userId' => $this->options['userId'])), new IsStudentRole()),
                    'choices' => $this->getAllRoleByLanguage($this->getRequest()->getLocale()),
                    'property' => 'roleType.languages.values[0].description'
                        )
                )
                ->add('fathers', null, array(
                    'choices' => $fathers,
                    'multiple' => true,
                    'required' => false,
                    'label' => 'fathers',
                    'constraints' => array(new Count(array('min' => 0, 'max' => 2)))
                        )
                )
//                ->add('roles', 'sonata_type_collection', array('required' => true, 'by_reference' => false, 'label' => 'roles'), array(
//                    'edit' => 'inline',
//                    'inline' => 'table'
//                ))
                ->with("general")
                ->add('name', null, array("label" => 'name'))
                ->add('lastName', null, array("label" => 'last_name'))
                ->add('dateBorn', null, array(
                    "label" => 'date_born',
//                    'widget' => 'single_text',
                    'years' => range(date('Y') - 60, date('Y') + 10)
                        )
                )
                ->add('phone', null, array("label" => 'phone'))
                ->add('phoneMovil', null, array("label" => 'phone_movil'))
                ->add('email', null, array("label" => 'email', 'constraints' => new EmailNoRepeat(array('userId' => $this->options['userId']))))
                ->add('address', null, array("label" => 'address'))
                ->add('status', null, array("label" => 'status'))
                ->end()
        ;

        if (!$this->isChild()) {
            $formMapper
//            ->with('Options', array('collapsed' => true))
                    ->with('general')
//                    ->add('sexType','sonata_type_immutable_array', array(
//                'keys' => array(
////                    array('content', 'textarea', array()),
////                    array('public', 'checkbox', array()),
//                    array("asdf",'choice', array('choices' => array(1 => 'type 1', 2 => 'type 2')))
//                )))
//                    ->add('sexType', 'sonata_type_model')
//                    ->add('sexType', 'sonata_type_model_list', array(), array())
//                    ->add('sexType', 'sonata_type_model', array("choices" => $this->getSex(), "expanded" => false, 'label' => 'sex'))
                    ->add('sexType', null, array(
//                        'class' => 'Esolving\Eschool\CoreBundle\Entity\Type',
                        'group_by' => 'sexType',
                        'choices' => $this->getTypeByCategoryByLanguage('sex', $this->getRequest()->getLocale()),
                        'property' => 'languages.values[0].description',
                        'required' => true,
                        'label' => 'sex'
                    ))
                    ->add('groupBlodType', null, array(
                        'group_by' => 'groupBlodType',
                        'choices' => $this->getTypeByCategoryByLanguage('groupblod', $this->getRequest()->getLocale()),
                        'property' => 'languages.values[0].description',
                        'required' => true,
                        'label' => 'group_blod'
                    ))
                    ->add('distritType', null, array(
                        'group_by' => 'distritType',
                        'choices' => $this->getTypeByCategoryByLanguage('distrit', $this->getRequest()->getLocale()),
                        'property' => 'languages.values[0].description',
                        'required' => true,
                        'label' => 'distrit'
                    ))
                    ->add('headquarterType', null, array(
                        'group_by' => 'headquarterType',
                        'choices' => $this->getTypeByCategoryByLanguage('headquarter', $this->getRequest()->getLocale()),
                        'property' => 'languages.values[0].description',
                        'required' => true,
                        'label' => 'headquarter'
                    ))
                    ->add('sectionType', null, array(
                        'group_by' => 'sectionType',
                        'choices' => $this->getTypeByCategoryByLanguage('section', $this->getRequest()->getLocale()),
                        'property' => 'languages.values[0].description',
                        'required' => true,
                        'label' => 'section'
                    ))
            ;
        }
    }

    public function preUpdate($user) {
        $userId = $user->getId();
        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine')->getManager();
        $teacher = $em->getRepository('EsolvingEschoolUserBundle:Teacher')->findOneBy(array(
            'user' => $userId
        ));
        $student = $em->getRepository('EsolvingEschoolUserBundle:Student')->findOneBy(array(
            'user' => $userId
        ));
        $father = $em->getRepository('EsolvingEschoolUserBundle:Father')->findOneBy(array(
            'user' => $userId
        ));
        $deleteStudent = true;
        $deleteFather = true;
        $deleteTeacher = true;
        $form = $this->getForm();
        foreach ($form->get('rolesAccess')->getData() as $roleAccessV) {
            $rolesTypeArr[] = $roleAccessV->getRoleType()->getName();
        }
        if (in_array('ROLE_STUDENT', $rolesTypeArr)) {
            $deleteStudent = false;
            if (!$student) {
                $student = new Student();
                $student->setUser($user);
                $user->getStudents()->add($student);
            } else {
                $fathers = $em->getRepository('EsolvingEschoolUserBundle:Father')->findAll();
                foreach ($fathers as $fatherV) {
                    $student->getFathers()->removeElement($fatherV);
                }
            }

            foreach ($form->get('fathers')->getData() as $fatherV) {
                $student->getFathers()->add($fatherV);
            }
        }
        if (in_array('ROLE_TEACHER', $rolesTypeArr)) {
            $deleteTeacher = false;
            if (!$teacher) {
                $teacher = new Teacher();
                $teacher->setUser($user);
                $em->persist($teacher);
                $user->addTeacher($teacher);
            }
        }
        if (in_array('ROLE_FATHER', $rolesTypeArr)) {
            $deleteFather = false;
            if (!$father) {
                $father = new Father();
                $father->setUser($user);
                $em->persist($father);
                $user->addFather($father);
            }
        }
        if ($deleteFather) {
            if ($father) {
                $em->remove($father);
            }
        }
        if ($deleteStudent) {
            if ($student) {
                $em->remove($student);
            }
        }
        if ($deleteTeacher) {
            if ($teacher) {
                $em->remove($teacher);
            }
        }
    }

    public function prePersist($user) {
        $form = $this->getForm();
        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine')->getManager();
        foreach ($form->get('rolesAccess')->getData() as $roleAccessV) {
            $rolesTypeArr[] = $roleAccessV->getRoleType()->getName();
        }
        if (in_array('ROLE_STUDENT', $rolesTypeArr)) {

            $student = new Student();
            $student->setUser($user);
            $user->getStudents()->add($student);

            foreach ($form->get('fathers')->getData() as $fatherV) {
                $student->getFathers()->add($fatherV);
            }
        }
        if (in_array('ROLE_TEACHER', $rolesTypeArr)) {

            $teacher = new Teacher();
            $teacher->setUser($user);
            $em->persist($teacher);
            $user->addTeacher($teacher);
        }
        if (in_array('ROLE_FATHER', $rolesTypeArr)) {
            $father = new Father();
            $father->setUser($user);
            $em->persist($father);
            $user->addFather($father);
        }
    }

    public function postPersist($user) {
        $em = $this->getConfigurationPool()
                        ->getContainer()
                        ->get('doctrine')->getManager();
        $userId = $user->getId();
        $code = date("Y", time()) . str_repeat("0", 6 - strlen($userId)) . $userId;
        $password = substr(sha1($code), 0, 6);
        $factory = $this->getConfigurationPool()
                ->getContainer()
                ->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $encodePassword = $encoder->encodePassword($password, $user->getSalt());
        $user->setCode($code);
        $user->setPassword($encodePassword);
        $em->persist($user);
        $container = $this->getConfigurationPool()->getContainer();
        $message = \Swift_Message::newInstance()
                ->setSubject($container->get('translator')->trans('you_was_registered', array(), 'EsolvingEschoolUserBundle'))
                ->setFrom($container->getParameter('email_master'))
                ->setTo($user->getEmail())
                ->setBody($container->get('templating')->render('EsolvingEschoolUserBundle:User:register.txt.twig', array('user' => $user, 'password' => $password)), 'text/html')
        ;
        $this->getConfigurationPool()->getContainer()->get('mailer')->send($message);
        $em->flush();
    }

    public function createQuery($context = 'list') {
        $query = $this->getModelManager()->createQuery($this->getClass());
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $queryUsers = $em->getRepository('EsolvingEschoolUserBundle:User')->findAllByLanguageToSonataAdmin($this->getRequest()->getLocale(), $query);
        return $queryUsers;
    }

    public function configureShowFields(ShowMapper $showMapper) {
        $showMapper
//                ->add('rolesAccess', null, array("label" => "roles"))
                ->add('rolesAccess', null, array("label" => "roles", 'template' => 'EsolvingEschoolBackendBundle:Show:rolesAccess.html.twig'))
                ->add('name', null, array("label" => "name"))
                ->add('lastName', null, array("label" => "last_name"))
                ->add('dateBorn', null, array("label" => "date_born"))
                ->add('sexType', null, array("label" => "sex", 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('phone', null, array("label" => "phone"))
                ->add('phoneMovil', null, array("label" => "phone_movil"))
                ->add('email', null, array("label" => "email"))
                ->add('address', null, array("label" => "address"))
                ->add('code', null, array("label" => "code"))
                ->add('createdAt', null, array("label" => "date_registered"))
                ->add('updatedAt', null, array("label" => "date_modificated"))
                ->add('disabledAt', null, array("label" => "date_disabled"))
                ->add('distritType', null, array("label" => "distrit", 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('groupBlodType', null, array("label" => "group_blod", 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('sectionType', null, array("label" => "section", 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('headquarterType', null, array("label" => "headquarter", 'template' => 'EsolvingEschoolBackendBundle:Show:type.html.twig'))
                ->add('status', null, array("label" => "status"))
        ;
    }

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('rolesAccess', null, array("label" => 'roles', 'template' => 'EsolvingEschoolBackendBundle:List:rolesAccess.html.twig'))
                ->addIdentifier('name', null, array("label" => 'name'))
                ->add('lastName', null, array("label" => 'last_name'))
                ->add('dateBorn', null, array("label" => 'date_born'))
                ->add('sexType', null, array("label" => 'sex', 'template' => 'EsolvingEschoolBackendBundle:List:type.html.twig'))
                ->add('phone', null, array("label" => 'phone'))
                ->add('phoneMovil', null, array("label" => 'phone_movil'))
                ->add('email', null, array("label" => 'email'))
                ->add('address', null, array("label" => 'address'))
                ->add('code', null, array("label" => 'code'))
//                ->add('password', null, array("label" => 'password'))
                ->add('createdAt', null, array("label" => 'date_registered'))
                ->add('updatedAt', null, array("label" => 'date_modificated'))
                ->add('disabledAt', null, array("label" => 'date_disabled'))
                ->add('distritType', null, array("label" => 'distrit', 'template' => 'EsolvingEschoolBackendBundle:List:type.html.twig'))
                ->add('groupBlodType', null, array("label" => 'group_blod', 'template' => 'EsolvingEschoolBackendBundle:List:type.html.twig'))
                ->add('sectionType', null, array("label" => 'section', 'template' => 'EsolvingEschoolBackendBundle:List:type.html.twig'))
                ->add('headquarterType', null, array("label" => 'headquarter', 'template' => 'EsolvingEschoolBackendBundle:List:type.html.twig'))
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
//        $datagridMapper
//                ->add('tags', null, array('filter_field_options' => array('expanded' => true, 'multiple' => true)))
//        ;        
        $datagridMapper
//                ->add('name', null, array("label" => 'name'))
//                ->add('lastName', null, array("label" => 'last_name'))
//                ->add('dateBorn', null, array("label" => 'date_born'))
//                ->add('phone', null, array("label" => 'phone'))
//                ->add('phoneMovil', null, array("label" => 'phone_movil'))
//                ->add('email', null, array("label" => 'email'))
//                ->add('address', null, array("label" => 'address'))
//                ->add('code', null, array("label" => 'code'))
//                ->add('password', null, array("label" => 'password'))
//                ->add('createdAt', null, array("label" => 'date_registered'))
//                ->add('updatedAt', null, array("label" => 'date_modificated'))
//                ->add('disabledAt', null, array("label" => 'date_disabled'))
//                ->add('sexType', null, array('label'=>'sex'), null, array('choices' => $this->getTypeByCategoryByLanguage('sex', $this->getRequest()->getLocale())))
//                ->add('groupBlodType', null, array('field_options' => array('choices' => $this->getTypeByCategoryByLanguage('groupblod', $this->getRequest()->getLocale())), "label" => 'group_blod'))
//                ->add('distritType', null, array('field_options' => array('choices' => $this->getTypeByCategoryByLanguage('distrit', $this->getRequest()->getLocale())), "label" => 'distrit'))
//                ->add('headquarterType', null, array('field_options' => array('choices' => $this->getTypeByCategoryByLanguage('headquarter', $this->getRequest()->getLocale())), "label" => "headquarter"))
//                ->add('sectionType', null, array('field_options' => array('choices' => $this->getTypeByCategoryByLanguage('section', $this->getRequest()->getLocale())), "label" => "section"))
//                ->add('status', null, array("label" => 'status'))
        ;
    }

}