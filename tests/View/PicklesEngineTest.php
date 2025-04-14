<?php

namespace Pickles\Tests\View;

use PHPUnit\Framework\TestCase;
use Pickles\View\PicklesEngine;

class PicklesEngineTest extends TestCase
{
    public function test_renders_template_with_parameters()
    {
        $param1 = "something";
        $param2 = 123;

        $expectedHtml = "
        <!DOCTYPE html>
        <html>
        <body>
            <h1>{$param1}</h1>
            <h1>{$param2}</h1>
        </body>
        </html>
        ";

        $engine = new PicklesEngine(__DIR__ . "/views");
        $content = $engine->render("test", compact("param1", "param2"), "layout");

        $removeWhiteSpaces = fn ($value) => preg_replace("/\s*/", "", $value);

        $this->assertEquals(
            $removeWhiteSpaces($expectedHtml),
            $removeWhiteSpaces($content)
        );
    }
}
