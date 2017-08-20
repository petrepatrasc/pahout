<?php

namespace Pahout\Test\Tool;

use PHPUnit\Framework\TestCase;
use Pahout\Test\helper\PahoutHelper;
use Pahout\Tool\SquareBracketSyntax;
use Pahout\Hint;
use Pahout\Logger;
use Pahout\Config;
use Symfony\Component\Console\Output\ConsoleOutput;

class SquareBracketSyntaxTest extends TestCase
{
    public function setUp()
    {
        Logger::getInstance(new ConsoleOutput());
    }

    public function test_array_push_with_single_element()
    {
        $code = <<<'CODE'
<?php
array_push($array, 1);
CODE;
        $root = \ast\parse_code($code, Config::AST_VERSION);

        $tester = PahoutHelper::create(new SquareBracketSyntax());
        $tester->test($root);

        $this->assertEquals(
            [
                new Hint(
                    'SquareBracketSyntax',
                    'Since `array_push()` has the function call overhead, let\'s use `$array[] =`.',
                    './test.php',
                    2,
                    Hint::DOCUMENT_LINK.'/SquareBracketSyntax.md'
                )
            ],
            $tester->hints
        );
    }

    public function test_array_push_with_multiple_elements()
    {
        $code = <<<'CODE'
<?php
array_push($array, 1, 2);
CODE;
        $root = \ast\parse_code($code, Config::AST_VERSION);

        $tester = PahoutHelper::create(new SquareBracketSyntax());
        $tester->test($root);

        $this->assertEmpty($tester->hints);
    }

    public function test_array_push_with_unpack_elements()
    {
        $code = <<<'CODE'
<?php
array_push($array, ...$list);
CODE;
        $root = \ast\parse_code($code, Config::AST_VERSION);

        $tester = PahoutHelper::create(new SquareBracketSyntax());
        $tester->test($root);

        $this->assertEmpty($tester->hints);
    }

    public function test_square_bracket_syntax()
    {
        $code = <<<'CODE'
<?php
$array[] = 1;
CODE;
        $root = \ast\parse_code($code, Config::AST_VERSION);

        $tester = PahoutHelper::create(new SquareBracketSyntax());
        $tester->test($root);

        $this->assertEmpty($tester->hints);
    }
}
