<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Path\Event\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use IDCI\Bundle\StepBundle\Navigation\NavigatorInterface;

class JsConfirmPathEventAction extends AbstractPathEventAction
{
    /**
     * {@inheritdoc}
     */
    protected function doExecute(
        FormInterface $form,
        NavigatorInterface $navigator,
        $i,
        $parameters = array()
    )
    {
        $form
            ->add('_js_confirm', 'idci_step_action_form_js_confirm', array_merge(
                $parameters,
                array('path_index' => $i)
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultParameters(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('message'));
    }
}