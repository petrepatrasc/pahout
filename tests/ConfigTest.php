<?php declare(strict_types=1);

namespace Pahout\Test;

use PHPUnit\Framework\TestCase;
use Pahout\Config;
use Pahout\Logger;
use Pahout\ToolBox;
use Pahout\Exception\InvalidConfigFilePathException;
use Pahout\Exception\InvalidConfigOptionException;
use Pahout\Exception\InvalidConfigOptionValueException;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConfigTest extends TestCase
{
    private const FIXTURE_PATH = __DIR__.'/fixtures';

    public function setUp()
    {
        Logger::getInstance(new ConsoleOutput());
    }

    public function test_default_config()
    {
        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_vendor');

            Config::load([
                'php-version' => null,
                'ignore-tools' => null,
                'ignore-paths' => null,
                'extensions' => null,
                'vendor' => null,
                'format' => null,
                'only-tools' => null,
            ]);
            $config = Config::getInstance();

            $this->assertEquals(phpversion(), $config->php_version);
            $this->assertEmpty($config->ignore_tools);
            $this->assertEquals([
                self::FIXTURE_PATH.'/with_vendor/vendor/test.php'
            ], $config->ignore_paths);
            $this->assertEquals(['php'], $config->extensions);
            $this->assertFalse($config->vendor);
            $this->assertEquals('pretty', $config->format);
        } finally {
            chdir($work_dir);
        }
    }

    public function test_specified_config()
    {
        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_config_file');

            Config::load([
                'php-version' => '7.1.0',
                'ignore-tools' => ['ShortArraySyntax'],
                'ignore-paths' => ['tests'],
                'extensions' => ['php', 'module', 'inc'],
                'vendor' => true,
                'format' => 'json',
                'only-tools' => array_diff(ToolBox::VALID_TOOLS, ['ElvisOperator']),
            ]);
            $config = Config::getInstance();

            $this->assertEquals('7.1.0', $config->php_version);
            $this->assertEquals(['ShortArraySyntax', 'ElvisOperator'], array_values($config->ignore_tools));
            $this->assertEquals([
                self::FIXTURE_PATH.'/with_config_file/tests/test1.php'
            ], $config->ignore_paths);
            $this->assertEquals(['php', 'module', 'inc'], $config->extensions);
            $this->assertTrue($config->vendor);
            $this->assertEquals('json', $config->format);
        } finally {
            chdir($work_dir);
        }
    }

    public function test_default_config_with_2_digit_version()
    {
        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_vendor');

            Config::load([
                'php-version' => '7.1.25',
                'ignore-tools' => null,
                'ignore-paths' => null,
                'extensions' => null,
                'vendor' => null,
                'format' => null,
                'only-tools' => null,
            ]);
            $config = Config::getInstance();

            $this->assertEquals('7.1.25', $config->php_version);
            $this->assertEmpty($config->ignore_tools);
            $this->assertEquals([
                self::FIXTURE_PATH.'/with_vendor/vendor/test.php'
            ], $config->ignore_paths);
            $this->assertFalse($config->vendor);
            $this->assertEquals('pretty', $config->format);
        } finally {
            chdir($work_dir);
        }
    }

    public function test_default_config_with_development_version()
    {
        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_vendor');

            Config::load([
                'php-version' => '7.4.0-dev',
                'ignore-tools' => null,
                'ignore-paths' => null,
                'extensions' => null,
                'vendor' => null,
                'format' => null,
                'only-tools' => null,
            ]);
            $config = Config::getInstance();

            $this->assertEquals('7.4.0', $config->php_version);
            $this->assertEmpty($config->ignore_tools);
            $this->assertEquals([
                self::FIXTURE_PATH.'/with_vendor/vendor/test.php'
            ], $config->ignore_paths);
            $this->assertFalse($config->vendor);
            $this->assertEquals('pretty', $config->format);
        } finally {
            chdir($work_dir);
        }
    }

    public function test_default_config_with_RC_version()
    {
        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_vendor');

            Config::load([
                'php-version' => '4.3.2RC1',
                'ignore-tools' => null,
                'ignore-paths' => null,
                'extensions' => null,
                'vendor' => null,
                'format' => null,
                'only-tools' => null,
            ]);
            $config = Config::getInstance();

            $this->assertEquals('4.3.2', $config->php_version);
            $this->assertEmpty($config->ignore_tools);
            $this->assertEquals([
                self::FIXTURE_PATH.'/with_vendor/vendor/test.php'
            ], $config->ignore_paths);
            $this->assertFalse($config->vendor);
            $this->assertEquals('pretty', $config->format);
        } finally {
            chdir($work_dir);
        }
    }

    public function test_with_config_file()
    {
        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_config_file');

            Config::load([
                'php-version' => null,
                'ignore-tools' => null,
                'ignore-paths' => null,
                'extensions' => null,
                'vendor' => null,
                'format' => null,
                'only-tools' => null,
            ], 'custom_config.yaml');
            $config = Config::getInstance();

            $this->assertEquals('7.0.0', $config->php_version);
            $this->assertEquals(['ShortArraySyntax'], array_values($config->ignore_tools));
            $this->assertEquals([
                self::FIXTURE_PATH.'/with_config_file/tests/test1.php',
                self::FIXTURE_PATH.'/with_config_file/bin/test1.php',
                self::FIXTURE_PATH.'/with_config_file/bin/test2.php',
            ], $config->ignore_paths);
            $this->assertEquals(["php", "module", "inc"], $config->extensions);
            $this->assertTrue($config->vendor);
            $this->assertEquals('pretty', $config->format);
        } finally {
            chdir($work_dir);
        }
    }

    public function test_specified_config_with_config_file()
    {
        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_config_file');

            Config::load([
                'php-version' => '7.1.0',
                'ignore-tools' => ['SyntaxError'],
                'ignore-paths' => ['tests'],
                'extensions' => null,
                'vendor' => null,
                'format' => null,
                'only-tools' => null,
            ], 'custom_config.yaml');
            $config = Config::getInstance();

            $this->assertEquals('7.1.0', $config->php_version);
            $this->assertEquals(['SyntaxError'], array_values($config->ignore_tools));
            $this->assertEquals([
                self::FIXTURE_PATH.'/with_config_file/tests/test1.php'
            ], $config->ignore_paths);
            $this->assertTrue($config->vendor);
            $this->assertEquals('pretty', $config->format);
        } finally {
            chdir($work_dir);
        }
    }

    public function test_throw_exception_when_specified_config_file_not_found()
    {
        $this->expectException(InvalidConfigFilePathException::class);
        $this->expectExceptionMessage('`invalid_config_file.yaml` is not found.');

        Config::load([
            'php-version' => null,
            'ignore-tools' => null,
            'ignore-paths' => null,
            'extensions' => null,
            'vendor' => null,
            'format' => null,
            'only-tools' => null,
        ], 'invalid_config_file.yaml');
    }

    public function test_throw_exception_when_include_invalid_key_in_config_file()
    {
        $this->expectException(InvalidConfigOptionException::class);
        $this->expectExceptionMessage('`invalid` is an invalid option.');

        $work_dir = getcwd();
        try {
            chdir(self::FIXTURE_PATH.'/with_config_file');

            Config::load([
                'php-version' => null,
                'ignore-tools' => null,
                'ignore-paths' => null,
                'extensions' => null,
                'vendor' => null,
                'format' => null,
                'only-tools' => null,
            ], 'invalid_config.yaml');
        } finally {
            chdir($work_dir);
        }
    }

    public function test_throw_exception_when_specified_an_invalid_version()
    {
        $this->expectException(InvalidConfigOptionValueException::class);
        $this->expectExceptionMessage('`7.1` is an invalid PHP version. Please specify the correct version such as `7.1.8`.');

        Config::load([
            'php-version' => '7.1',
            'ignore-tools' => null,
            'ignore-paths' => null,
            'extensions' => null,
            'vendor' => null,
            'format' => null,
            'only-tools' => null,
        ]);
    }

    public function test_throw_exception_when_specified_an_invalid_tools_as_ignore_tools()
    {
        $this->expectException(InvalidConfigOptionValueException::class);
        $this->expectExceptionMessage('`invalid_tool` is an invalid tool. Please check the correct tool list.');

        Config::load([
            'php-version' => null,
            'ignore-tools' => ['invalid_tool'],
            'ignore-paths' => null,
            'extensions' => null,
            'vendor' => null,
            'format' => null,
            'only-tools' => null,
        ]);
    }

    public function test_throw_exception_when_specified_an_invalid_tools_as_only_tools()
    {
        $this->expectException(InvalidConfigOptionValueException::class);
        $this->expectExceptionMessage('`invalid_tool` is an invalid tool. Please check the correct tool list.');

        Config::load([
            'php-version' => null,
            'ignore-tools' => null,
            'ignore-paths' => null,
            'extensions' => null,
            'vendor' => null,
            'format' => null,
            'only-tools' => ['invalid_tool'],
        ]);
    }

    public function test_throw_exception_when_specified_an_invalid_paths()
    {
        $this->expectException(InvalidConfigOptionValueException::class);
        $this->expectExceptionMessage('`tests` is invalid paths. It must be array.');

        Config::load([
            'php-version' => null,
            'ignore-tools' => null,
            'ignore-paths' => 'tests',
            'extensions' => null,
            'vendor' => null,
            'format' => null,
            'only-tools' => null,
        ]);
    }

    public function test_throw_exception_when_specified_an_invalid_extensions()
    {
        $this->expectException(InvalidConfigOptionValueException::class);
        $this->expectExceptionMessage('`php` is invalid extensions. It must be array.');

        Config::load([
            'php-version' => null,
            'ignore-tools' => null,
            'ignore-paths' => null,
            'extensions' => "php",
            'vendor' => null,
            'format' => null,
            'only-tools' => null,
        ]);
    }

    public function test_throw_exception_when_specified_an_invalid_vendor()
    {
        $this->expectException(InvalidConfigOptionValueException::class);
        $this->expectExceptionMessage('`yes` is an invalid vendor flag. It must be `true` or `false`.');

        Config::load([
            'php-version' => null,
            'ignore-tools' => null,
            'ignore-paths' => null,
            'extensions' => null,
            'vendor' => 'yes',
            'format' => null,
            'only-tools' => null,
        ]);
    }

    public function test_throw_exception_when_specified_an_invalid_format()
    {
        $this->expectException(InvalidConfigOptionValueException::class);
        $this->expectExceptionMessage('`xml` is an invalid format. It must be `pretty` or `json`.');

        Config::load([
            'php-version' => null,
            'ignore-tools' => null,
            'ignore-paths' => null,
            'extensions' => null,
            'vendor' => null,
            'format' => 'xml',
            'only-tools' => null,
        ]);
    }
}
