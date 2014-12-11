<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */
 
namespace IDCI\Bundle\StepBundle\Step\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentStepType extends AbstractStepType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'content';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array('content' => null))
            ->setAllowedTypes('content', array('null', 'string'))
        ;
    }
}