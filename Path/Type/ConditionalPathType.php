<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */
 
namespace IDCI\Bundle\StepBundle\Path\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConditionalPathType extends MultiplePathType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'conditional';
    }
}