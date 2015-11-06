<?php
/**
 * Date: 15/10/14
 * Time: 17:50
 */

namespace Hoimi;


class ArrayContainerTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    public function setUp()
    {
        $this->target = new ArrayContainer(array(
            'inner' => array('innerValue' => 'value'),
            'flat' => 'flatValue',
            'nullValue' => null,
            'zeroValue' => 0,
            'emptyString' => '',
            'falseValue' => false,
        ));
    }

    public function testGetRecursive ()
    {
        $this->assertSame('value', $this->target->get('inner.innerValue'));
    }

    public function testGetFlat ()
    {
        $this->assertSame('flatValue', $this->target->get('flat'));
    }

    public function testGetArrayAccess ()
    {
        $this->assertSame('value', $this->target['inner']['innerValue']);
    }

    public function testGetDefault ()
    {
        $this->assertSame('default', $this->target->get('none', 'default'));
    }

    public function testGetRecursiveFail ()
    {
        $this->assertSame(null, $this->target->get('inner.innerValue.superInnerValue'));
        $this->assertSame(null, $this->target->get('inner.innerValue2'));
    }

    public function testGetValue ()
    {
        $this->assertSame(null, $this->target->get('nullValue'));
        $this->assertSame(0, $this->target->get('zeroValue'));
        $this->assertSame('', $this->target->get('emptyString'));
        $this->assertSame(false, $this->target->get('falseValue'));
    }
}