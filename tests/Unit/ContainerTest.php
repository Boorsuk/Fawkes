<?php

declare(strict_types = 1);

namespace Tests\Unit;

use Fawkes\Container;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class ContainerTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        $this->container = Container::init();
        $this->container->flush();
    }

    public function tearDown() : void
    {
        $this->container->flush();
    }

    /**
     * @test
     */
    public function should_return_empty_entries_on_first_init()
    {
        $entries = $this->container->entries();

        assertEmpty($entries);
    }

    /**
     * @test
     */
    public function should_has_returned_true_when_id_is_in_entry()
    {
        $id = 'BindedId';   
        $this->container->bind($id, 'ShouldBeTrue');
        
        $isPresent = $this->container->has($id);

        assertTrue($isPresent);
    }

    /**
     * @test 
     */
    public function should_return_the_same_instance_on_multiple_init()
    {
        $containerOne = Container::init();
        $containerTwo = Container::init();

        assertSame($containerOne, $containerTwo);
    }
}