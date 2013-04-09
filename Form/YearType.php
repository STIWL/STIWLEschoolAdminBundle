<?php

namespace Esolving\Eschool\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\DependencyInjection\ContainerInterface;

//use Acme\DemoBundle\Form\EventListener\addTranslatedFieldSubscriber;

class YearType extends AbstractType
{
//    protected $container;

//    public function __construct(ContainerInterface $container)
//    {
//        $this->container = $container;
//    }
    public function getName()
    {
        return 'year_field';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(! class_exists($options['personal_translation']))
        {
            Throw new \InvalidArgumentException(sprintf("Unable to find personal translation class: '%s'", $options['personal_translation']));
        }
        if(! $options['field'])
        {
            Throw new \InvalidArgumentException("You should provide a field to translate");
        }

//        $subscriber = new addTranslatedFieldSubscriber($builder->getFormFactory(), $this->container, $options);
//        $builder->addEventSubscriber($subscriber);
    }

    public function getDefaultOptions(array $options = array())
    {
        $options['remove_empty'] = true; //Personal Translations without content are removed
        $options['csrf_protection'] = false; 
        $options['personal_translation'] = false; //Personal Translation class
        $options['locales'] = array('en', 'es'); //the locales you wish to edit
        $options['required_locale'] = array('es'); //the required locales cannot be blank
        $options['field'] = false; //the field that you wish to translate
        $options['widget'] = "text"; //change this to another widget like 'texarea' if needed
        $options['entity_manager_removal'] = true; //auto removes the Personal Translation thru entity manager

        return $options;
    }
}