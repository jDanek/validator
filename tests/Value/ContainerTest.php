<?php

namespace Danek\Validator\Tests\Value;

use Danek\Validator\Value\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @var Container */
    protected $container;

    public function testSet()
    {
        $setters = [
            'id' => 8,
            'data' => 42,
            'name' => 'Eight',
            'point' => 'None',
        ];

        $expected = [
            'id' => 8,
            'data' => 42,
            'name' => 'Eight',
            'point' => 'None',
        ];

        foreach ($setters as $key => $value) {
            $this->container->set($key, $value);
        }

        $this->assertSame($expected, $this->container->getArrayCopy());
    }

    public function testSetTraverse()
    {
        $setters = [
            'test.sub.id' => 8,
            'test.data' => 42,
            'test.sub.name' => 'Eight',
            'test.sub.point' => 'None',
        ];

        $expected = [
            'test' => [
                'sub' => [
                    'id' => 8,
                    'name' => 'Eight',
                    'point' => 'None',
                ],
                'data' => 42,
            ],
        ];

        foreach ($setters as $key => $value) {
            $this->container->set($key, $value);
        }

        $this->assertSame($expected, $this->container->getArrayCopy());
    }

    public function testGet()
    {
        $container = new Container(['data' => '', 'parent' => ['sub' => 8]]);

        $this->assertFalse($container->get('data.unknown'));
        $this->assertSame(8, $container->get('parent.sub'));
    }

    protected function setUp(): void
    {
        $this->container = new Container();
    }
}
