<?php
/**
 * Created by iBROWS AG.
 * User: marcsteiner
 * Date: 11.03.14
 * Time: 13:05
 */

namespace Ibrows\LoggableBundle\Form;


use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduledChangeableTypeExtension extends AbstractTypeExtension
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['scheduledchangeable'] === false) {
            return;
        }

        $format = "yyyy-MM-dd";
        if(isset($options['scheduledchangeable_format'])){
            $format = $options['scheduledchangeable_format'];
        }

        $propertyName = 'scheduledChangeDate';
        if ($options['scheduledchangeable'] === 'auto') {
            if (!$this->checkIsScheduledChangeable($builder, $propertyName)) {
                return;
            }
        } else {
            if ($options['scheduledchangeable'] !== true) {
                $propertyName = $options['scheduledchangeable'];
            }
        }

        $builder->add($propertyName, 'date', array('widget' => 'single_text', 'format' => $format,'required'=>false));

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('scheduledchangeable' => 'auto'));
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $property
     * @return bool
     */
    protected function checkIsScheduledChangeable(FormBuilderInterface $builder, $property)
    {
        $entity = $builder->getData();
        if ($entity == null || !is_object($entity))  {
            return false;
        }
        $class = get_class($entity);
        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->implementsInterface('\Ibrows\LoggableBundle\Model\ScheduledChangeable')) {
            return false;
        }
        if(!method_exists($entity,'getId') || !$entity->getId()){
            return false;
        }
        if (property_exists($class, $property)) {
            return true;
        }

        return false;
    }
}
