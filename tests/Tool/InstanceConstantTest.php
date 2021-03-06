<?php declare(strict_types=1);

namespace Pahout\Test\Tool;

use PHPUnit\Framework\TestCase;
use Pahout\Test\helper\PahoutHelper;
use Pahout\Tool\InstanceConstant;
use Pahout\Hint;
use Pahout\Logger;
use Pahout\Config;
use Symfony\Component\Console\Output\ConsoleOutput;

class InstanceConstantTest extends TestCase
{
    public function setUp()
    {
        Logger::getInstance(new ConsoleOutput());
    }

    public function test_get_class_with_constants()
    {
        $code = <<<'CODE'
<?php
$instance = new Hoge();
get_class($instance)::CONSTANT;
CODE;
        $root = \ast\parse_code($code, Config::AST_VERSION);

        $tester = PahoutHelper::create(new InstanceConstant());
        $tester->test($root);

        $this->assertEquals(
            [
                new Hint(
                    'InstanceConstant',
                    'You can access class constants from instances.',
                    './test.php',
                    3,
                    Hint::DOCUMENT_LINK.'/InstanceConstant.md'
                )
            ],
            $tester->hints
        );
    }

    public function test_instance_constants()
    {
        $code = <<<'CODE'
<?php
$instance = new Hoge();
$instance::CONSTANT;
CODE;
        $root = \ast\parse_code($code, Config::AST_VERSION);

        $tester = PahoutHelper::create(new InstanceConstant());
        $tester->test($root);

        $this->assertEmpty($tester->hints);
    }
}
