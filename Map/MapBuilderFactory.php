<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Map;

use IDCI\Bundle\StepBundle\Step\StepBuilderInterface;
use IDCI\Bundle\StepBundle\Path\PathBuilderInterface;
use IDCI\Bundle\StepBundle\Map\MapNavigatorInterface;

class MapBuilderFactory implements MapBuilderFactoryInterface
{
    /**
     * @var StepBuilderInterface
     */
    private $stepBuilder;

    /**
     * @var PathBuilderInterface
     */
    private $pathBuilder;

    /**
     * @var MapNavigatorInterface
     */
    private $mapNavigator;

    /**
     * Constructor
     *
     * @param StepBuilderInterface  $stepBuilder  The step builder.
     * @param PathBuilderInterface  $pathBuilder  The path builder.
     * @param MapNavigatorInterface $mapNavigator The map navigator.
     */
    public function __construct(
        StepBuilderInterface $stepBuilder,
        PathBuilderInterface $pathBuilder,
        MapNavigatorInterface $mapNavigator
    )
    {
        $this->stepBuilder = $stepBuilder;
        $this->pathBuilder = $pathBuilder;
        $this->mapNavigator = $mapNavigator;
    }

    /**
     * {@inheritdoc}
     */
    public function createBuilder(array $data = array(), array $options = array())
    {
        return $this->createNamedBuilder($name = 'map', $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedBuilder($name, array $data = array(), array $options = array())
    {
        return new MapBuilder(
            $name,
            $data,
            $options,
            $this->stepBuilder,
            $this->pathBuilder,
            $this->mapNavigator
        );
    }
}