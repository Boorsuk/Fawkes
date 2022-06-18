<?php

declare(strict_types = 1);

namespace Tests\Unit;

use Fawkes\Container;
use Fawkes\Exceptions\BuiltinTypeOccuredException;
use Fawkes\Exceptions\ContainerException;
use Fawkes\Exceptions\MissingTypeHintException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\Lego\Interfaces\DummyInterface;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
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
    public function entries_should_return_empty_entries_on_first_init()
    {
        $entries = $this->container->entries();

        assertEmpty($entries);
    }

    /**
     * @test
     */
    public function has_should_return_true_when_id_is_in_entry()
    {
        $id = 'BindedId';   
        $this->container->bind($id, 'ShouldBeTrue');
        
        $isPresent = $this->container->has($id);

        assertTrue($isPresent);
    }

    /**
     * @test 
     */
    public function init_should_return_the_same_instance_on_multiple_init()
    {
        $containerOne = Container::init();
        $containerTwo = Container::init();

        assertSame($containerOne, $containerTwo);
    }

    /**
     * @test
     */
    public function get_should_return_proper_instance_of_an_interface_when_string_is_present_in_entry()
    {
        $dummyAnonymouseClass = new class() implements DummyInterface{};

        /** @var MockObject */
        $containerMock = $this->getMockBuilder(Container::class)
                              ->disableOriginalConstructor()
                              ->onlyMethods(['resolve'])
                              ->getMock();
        
        $containerMock->expects($this->once())
                      ->method('resolve')
                      ->with($dummyAnonymouseClass::class)
                      ->will($this->returnValue(new $dummyAnonymouseClass()));

        $containerMock->bind(DummyInterface::class, $dummyAnonymouseClass::class);

        // when
        $returnedInstance = $containerMock->get(DummyInterface::class);

        // then
        assertInstanceOf(DummyInterface::class, $returnedInstance);
        assertInstanceOf($dummyAnonymouseClass::class, $returnedInstance);
    }

    /**
     * @test
     */
    public function get_should_return_proper_instance_of_an_interface_when_callback_is_present_in_entry()
    {
        $dummyAnonymouseClass = new class() implements DummyInterface{};

        /** @var MockObject */
        $containerMock = $this->getMockBuilder(Container::class)
                              ->disableOriginalConstructor()
                              ->onlyMethods(['resolve'])
                              ->getMock();
        
        $containerMock->expects($this->never())
                      ->method('resolve');
        
        $containerMock->bind(DummyInterface::class, function() use($dummyAnonymouseClass){
            return new $dummyAnonymouseClass();
        });

        // when
        $returnedInstance = $containerMock->get(DummyInterface::class);

        // then
        assertInstanceOf(DummyInterface::class, $returnedInstance);
        assertInstanceOf($dummyAnonymouseClass::class, $returnedInstance);
    }

    /**
     * @test
     */
    public function get_should_call_resolve_when_id_is_not_present_in_entry_and_return_instance_on_success()
    {
        $dummyAnonymouseClass = new class() {};
        /** @var MockObject */
        $containerMock = $this->getMockBuilder(Container::class)
                              ->disableOriginalConstructor()
                              ->onlyMethods(['resolve'])
                              ->getMock();

        $containerMock->expects($this->once())
                      ->method('resolve')
                      ->with($dummyAnonymouseClass::class)
                      ->will($this->returnValue(new $dummyAnonymouseClass()));

        // when
        $returnedInstance = $containerMock->get($dummyAnonymouseClass::class);

        //then
        assertEmpty($containerMock->entries());
        assertTrue($returnedInstance instanceof $dummyAnonymouseClass);
    }

    /**
     * @test
     */
    public function get_should_throw_not_found_exception_when_couldnt_resolve_entry()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $id = 'MissingClass';

        /** @var MockObject */
        $containerMock = $this->getMockBuilder(Container::class)
                              ->disableOriginalConstructor()
                              ->onlyMethods(['resolve', 'has'])
                              ->getMock();

        $containerMock->expects($this->once())
                      ->method('resolve')
                      ->with($id)
                      ->will($this->returnValue(null));
        
        $containerMock->expects($this->once())
                      ->method('has')
                      ->with($id)
                      ->will($this->returnValue(false));

        // then
        $containerMock->get($id);
    }

    /**
     * @test
     */
    public function resolve_should_return_null_when_couldnt_create_reflection_class()
    {
        $id = 'MissingClassForTestingContainer';

        $result = $this->container->resolve($id);

        assertNull($result);
    }

    /**
     * @test
     */
    public function resolve_should_throw_container_exception_when_theres_no_entry_for_interface()
    {
        $this->expectException(ContainerExceptionInterface::class);

        $this->container->resolve(DummyInterface::class);
    }

    /**
     * @test
     */
    public function resolve_should_return_instance_of_class_when_theres_no_constructor_in_reflected_class()
    {
        $dummyAnonymouseClass = new class() {};

        $result = $this->container->resolve($dummyAnonymouseClass::class);

        assertTrue($result instanceof $dummyAnonymouseClass);
    }

    /**
     * @test
     */
    public function resolve_should_throw_container_exception_when_theres_no_type_hinting()
    {
        $dummyAnonymouseClass = new class(true) {
            public function __construct($a) {}
        };

        $exception = null;
        try {
            $this->container->resolve($dummyAnonymouseClass::class);
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception);
        $this->assertInstanceOf(ContainerExceptionInterface::class, $exception);
        $this->assertInstanceOf(MissingTypeHintException::class, $exception);
    }

    /**
     * @test
     */
    public function resolve_should_throw_container_exception_when_theres_builtin_type_without_default_value()
    {
        $dummyAnonymouseClass = new class(true) {
            public function __construct(bool $a) {}
        };

        $exception = null;
        try {
            $this->container->resolve($dummyAnonymouseClass::class);
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception);
        $this->assertInstanceOf(ContainerExceptionInterface::class, $exception);
        $this->assertInstanceOf(BuiltinTypeOccuredException::class, $exception);
    }

    /**
     * @test
     */
    public function resolve_should_return_instance_of_class_when_builtin_parameter_has_default_value()
    {
        $dummyAnonymouseClass = new class(997) {
            public int $a;
            public function __construct(int $a = 112) {
                $this->a = $a;
            }
        };

        $result = $this->container->resolve($dummyAnonymouseClass::class);

        assertInstanceOf($dummyAnonymouseClass::class, $result);
        assertEquals(112, $result->a);
    }

    /**
     * @test
     */
    public function resolve_should_call_get_when_reflection_type_is_not_a_builtin_type()
    {
        $classA = new class() implements DummyInterface {};
        $classB = new class(new $classA(new $classA())) {
            public $a;
            public function __construct(DummyInterface $a) {
                $this->a = $a;
            }
        };

        /** @var MockObject */
        $containerMock = $this->getMockBuilder(Container::class)
                              ->disableOriginalConstructor()
                              ->onlyMethods(['get'])
                              ->getMock();

        $containerMock->expects($this->once())
                      ->method('get')
                      ->with(DummyInterface::class)
                      ->will($this->returnValue(new $classA()));
        
        // when
        $result = $containerMock->resolve($classB::class);

        // then
        assertInstanceOf($classB::class, $result);
        assertInstanceOf(DummyInterface::class, $result->a);
    }
}