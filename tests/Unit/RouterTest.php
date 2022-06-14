<?php

declare(strict_types = 1);

namespace Tests\Unit;

use Fawkes\Network\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    /**
     * @test 
     */
    public function should_return_empty_routes_on_init(){
        $this->assertEmpty((new Router)->routes());
    }
}