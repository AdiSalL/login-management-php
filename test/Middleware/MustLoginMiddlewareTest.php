<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Middleware;

use ProgrammerZamanNow\Belajar\PHP\MVC\Middleware\MustLoginMiddleWare;
use PHPUnit\Framework\TestCase;

class MustLoginMiddlewareTest extends TestCase {

    private MustLoginMiddelware $middleware;

    public function setUp():void {
        $this->middleware = new MustLoginMiddleWare();
    }

    public function testBefore() {

    }

}
