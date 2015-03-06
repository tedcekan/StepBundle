<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Navigation\Event;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use IDCI\Bundle\StepBundle\Navigation\NavigatorInterface;
use IDCI\Bundle\StepBundle\Step\Event\StepEventRegistryInterface;
use IDCI\Bundle\StepBundle\Path\Event\PathEventRegistryInterface;
use IDCI\Bundle\StepBundle\Flow\FlowData;

class NavigationEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var NavigatorInterface
     */
    protected $navigator;

    /**
     * @var StepEventRegistryInterface
     */
    protected $stepEventRegistry;

    /**
     * @var PathEventRegistryInterface
     */
    protected $pathEventRegistry;

    /**
     * Constructor
     *
     * @param NavigatorInterface         $navigator         The navigator.
     * @param StepEventRegistryInterface $stepEventRegistry The step event registry.
     * @param PathEventRegistryInterface $pathEventRegistry The path event registry.
     */
    public function __construct(
        NavigatorInterface $navigator,
        StepEventRegistryInterface $stepEventRegistry,
        PathEventRegistryInterface $pathEventRegistry
    )
    {
        $this->navigator         = $navigator;
        $this->stepEventRegistry = $stepEventRegistry;
        $this->pathEventRegistry = $pathEventRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA  => array(
                array('addStepEvents', 1),
                array('addPathEvents', 0),
            ),
            FormEvents::POST_SET_DATA => array(
                array('addStepEvents', 1),
                array('addPathEvents', 0),
            ),
            FormEvents::PRE_SUBMIT    => array(
                array('preSubmit', 99999),
                array('addStepEvents', 1),
                array('addPathEvents', 0),
            ),
            FormEvents::SUBMIT        => array(
                array('addStepEvents', 1),
                array('addPathEvents', 0),
            ),
            FormEvents::POST_SUBMIT   => array(
                array('postSubmit', 99999),
                array('addStepEvents', 1),
                array('addPathEvents', 0),
            ),
        );
    }

    /**
     * Add step events.
     *
     * @param FormEvent $event
     */
    public function addStepEvents(FormEvent $event)
    {
        $form          = $event->getForm();
        $retrievedData = array();

        $step = $this->navigator->getCurrentStep();
        $configuration = $step->getConfiguration();
        $events = $configuration['options']['events'];

        if (isset($events[$event->getName()])) {
            foreach ($events[$event->getName()] as $configuration) {
                $action = $this
                    ->stepEventRegistry
                    ->getAction($configuration['action'])
                ;

                $parameters = isset($configuration['parameters']) ?
                    $configuration['parameters'] :
                    array()
                ;

                $result = $action->execute(
                    $form,
                    $this->navigator,
                    $parameters
                );

                if (null !== $result) {
                    $retrievedData[$configuration['action']] = $result;
                }
            }
        }

        if (!empty($retrievedData)) {
            $this->navigator->getFlow()->setStepData(
                $this->navigator->getCurrentStep(),
                $retrievedData,
                array(),
                FlowData::TYPE_RETRIVED
            );
        }
    }

    /**
     * Add path events.
     *
     * @param FormEvent $event
     */
    public function addPathEvents(FormEvent $event)
    {
        $form          = $event->getForm();
        $retrievedData = array();

        foreach ($this->navigator->getCurrentPaths() as $i => $path) {
            $configuration = $path->getConfiguration();
            $events = $configuration['options']['events'];

            if (isset($events[$event->getName()])) {
                foreach ($events[$event->getName()] as $configuration) {
                    $action = $this
                        ->pathEventRegistry
                        ->getAction($configuration['action'])
                    ;

                    $parameters = isset($configuration['parameters']) ?
                        $configuration['parameters'] :
                        array()
                    ;

                    $result = $action->execute(
                        $form,
                        $this->navigator,
                        $i,
                        $parameters
                    );

                    if (null !== $result) {
                        $retrievedData[$configuration['action']] = $result;
                    }
                }
            }
        }

        if (!empty($retrievedData)) {
            $this->navigator->getFlow()->setStepData(
                $this->navigator->getCurrentStep(),
                $retrievedData,
                array(),
                FlowData::TYPE_RETRIVED
            );
        }
    }

    /**
     * Pre submit.
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (isset($data['_back'])) {
            $stepName = '1' == $data['_back'] ? null : $data['_back'];
            $this->navigator->goBack($stepName);
        }
    }

    /**
     * Post submit.
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (!$this->navigator->hasReturned() && isset($data['_data'])) {
            $this->navigator->setCurrentStepData(
                $data['_data'],
                $this->buildDataFormTypeMapping($event->getForm())
            );
        }
    }

    /**
     * Build data form type mapping.
     *
     * @param FormInterface $form The form.
     *
     * @return array
     */
    protected function buildDataFormTypeMapping(FormInterface $form)
    {
        $mapping = array();
        foreach($form->get('_data') as $key => $field) {
            $mapping[$key] = $field->getConfig()->getType()->getName();
        }

        return $mapping;
    }
}
