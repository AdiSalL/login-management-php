<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App;
use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {
    public function testRender() {
        View::render("Home/index", [
            "PHP Login Management"
        ]);
        $this->expectOutputRegex("[PHP Login Management]");
        $this->expectOutputRegex("[html]");
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Login]");

        
        
    }
}