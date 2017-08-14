<?php

namespace Pahout\Test\helper;

use Pahout\Pahout;
use Pahout\Tool\Base;
use \ast\Node;

class PahoutHelper
{
    private $pahout;
    public $hints = [];

    public function __construct(Pahout $pahout)
    {
        $this->pahout = $pahout;
    }

    public static function create(Base $tool)
    {
        $pahout = new Pahout([]);
        $klass = new \ReflectionClass('\Pahout\Pahout');
        $property = $klass->getProperty('tools');
        $property->setAccessible(true);
        $property->setValue($pahout, [$tool]);

        return new PahoutHelper($pahout);
    }

    public function test($node)
    {
        $method = new \ReflectionMethod('\Pahout\Pahout', 'traverse');
        $method->setAccessible(true);
        $method->invoke($this->pahout, './test.php', $node);

        $this->hints = $this->pahout->hints;
    }
}