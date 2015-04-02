<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Step\Event\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use IDCI\Bundle\StepBundle\Navigation\NavigatorInterface;

abstract class AbstractStepEventAction implements StepEventActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(
        FormInterface $form,
        NavigatorInterface $navigator,
        array $parameters = array(),
        $data = null
    )
    {
        $resolver = new OptionsResolver();
        $this->setDefaultParameters($resolver);

        return $this->doExecute(
            $form,
            $navigator,
            $resolver->resolve($parameters),
            $data
        );
    }

    /**
     * Set default parameters.
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultParameters(OptionsResolverInterface $resolver)
    {
    }

    /**
     * Do execute action.
     *
     * @param FormInterface      $form       The form.
     * @param NavigatorInterface $navigator  The navigator.
     * @param array              $parameters The resolved parameters.
     * @param mixed              $data       The retrieved event data.
     */
    abstract protected function doExecute(
        FormInterface $form,
        NavigatorInterface $navigator,
        $parameters = array(),
        $data = null
    );
}