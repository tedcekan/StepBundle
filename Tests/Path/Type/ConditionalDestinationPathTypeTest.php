<?php

namespace IDCI\Bundle\StepBundle\Tests\Path\Type;

use IDCI\Bundle\StepBundle\Path\Type\ConditionalDestinationPathType;
use IDCI\Bundle\StepBundle\Step\Step;

class ConditionalDestinationPathTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $securityContext = $this
            ->getMockBuilder("Symfony\Component\Security\Core\SecurityContextInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getToken', 'setToken', 'isGranted'))
            ->getMock()
        ;
        $securityContext
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null))
        ;

        $twigStringLoader = new \Twig_Loader_String;
        $twigEnvironment  = new \Twig_Environment($twigStringLoader, array());

        $this->pathType = new ConditionalDestinationPathType(
            $twigEnvironment,
            $securityContext,
            $this->getMock("Symfony\Component\HttpFoundation\Session\SessionInterface"),
            $this->getMock("IDCI\Bundle\StepBundle\ConditionalRule\ConditionalRuleRegistryInterface")
        );
    }

    public function testResolveDestination()
    {
        $flow = $this->getMock("IDCI\Bundle\StepBundle\Flow\FlowInterface");
        $flow
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(array(
                'data' => array(
                    'step1' => array(
                        'firstname' => "john",
                        'lastname'  => "doe",
                    )
                )
            )))
        ;

        $navigator = $this->getMock("IDCI\Bundle\StepBundle\Navigation\NavigatorInterface");
        $navigator
            ->expects($this->any())
            ->method('getFlow')
            ->will($this->returnValue($flow))
        ;

        $this->assertEquals(
            'destination_1',
            $this->pathType->resolveDestination(
                array(
                    'source'              => 'step1',
                    'default_destination' => 'default',
                    'destinations'        => array(
                        'destination_1' => true,
                        'destination_2' => true,
                    )
                ),
                $navigator
            )
        );

        $this->assertEquals(
            'destination_2',
            $this->pathType->resolveDestination(
                array(
                    'source'              => 'step1',
                    'default_destination' => 'default',
                    'destinations'        => array(
                        'destination_1' => false,
                        'destination_2' => true,
                    )
                ),
                $navigator
            )
        );

        $this->assertEquals(
            'default',
            $this->pathType->resolveDestination(
                array(
                    'source'              => 'step1',
                    'default_destination' => 'default',
                    'destinations'        => array(
                        'destination_1' => false,
                        'destination_2' => false,
                    )
                ),
                $navigator
            )
        );

        $this->assertEquals(
            'destination_2',
            $this->pathType->resolveDestination(
                array(
                    'source'              => 'step1',
                    'default_destination' => 'default',
                    'destinations'        => array(
                        'destination_1' => '{{ flow_data.data.step1.firstname == "dummy" }}',
                        'destination_2' => '{{ flow_data.data.step1.firstname == "john" }}',
                    )
                ),
                $navigator
            )
        );
    }

    public function testbuildPath()
    {
        $step1   = new Step(array('name' => 'step1'));
        $step2   = new Step(array('name' => 'step2'));
        $step3   = new Step(array('name' => 'step3'));
        $stepEnd = new Step(array('name' => 'stepEnd'));


        $path = $this->pathType->buildPath(
            array(
                'step1'   => $step1,
                'step2'   => $step2,
                'step3'   => $step3,
                'stepEnd' => $stepEnd,
            ),
            array(
                'source'              => 'step1',
                'default_destination' => 'stepEnd',
                'destinations'        => array(
                    'step2' => false,
                    'step3' => false,
                )
            )
        );

        $this->assertEquals($path->getSource(), $step1);
        $this->assertEquals(
            $path->getDestinations(),
            array(
                'step2'   => $step2,
                'step3'   => $step3,
                'stepEnd' => $stepEnd
            )
        );

        $flow = $this->getMock("IDCI\Bundle\StepBundle\Flow\FlowInterface");
        $flow
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(null))
        ;
        $navigator = $this->getMock("IDCI\Bundle\StepBundle\Navigation\NavigatorInterface");
        $navigator
            ->expects($this->any())
            ->method('getFlow')
            ->will($this->returnValue($flow))
        ;
        $this->assertEquals($path->resolveDestination($navigator), $stepEnd);
    }
}