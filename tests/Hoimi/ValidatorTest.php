<?php
namespace Hoimi;
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate ()
    {
        $request = new Request(array(), array(
            'mahotora' => '1979-12-01',
            'scruit' => 2,
            'remila' => 3,
            'zaolik' => '',
        ));
        $result = \Hoimi\Validator::validate($request, array(
            'hoimi' => array('required' => true),
            'mahotora' => array('required' => true, 'dataType' => 'date', 'min' => '1979-11-30', 'max' => '1979-12-02'),
            'scruit' => array('required' => true, 'dataType' => 'double', 'min' => 1, 'max' => 100),
            'remila' => array('required' => true, 'dataType' => 'double', ),
            'zaolik' => array('required' => true, 'dataType' => 'string', ),
        ));
        $this->assertSame(array('hoimi'=>'NOT_REQUIRED', 'zaolik' => 'NOT_REQUIRED'), $result);
    }

    public function testRequired ()
    {
        $this->assertTrue(\Hoimi\Validator::required(1));
        $this->assertTrue(\Hoimi\Validator::required(true));
        $this->assertTrue(\Hoimi\Validator::required(false));
        $this->assertTrue(\Hoimi\Validator::required(0));
        $this->assertFalse(\Hoimi\Validator::required(""));
        $this->assertFalse(\Hoimi\Validator::required(null));
    }


    public function testIsValidRange ()
    {
        $this->assertTrue(\Hoimi\Validator::isValidRange(1, 1, 1));
        $this->assertFalse(\Hoimi\Validator::isValidRange(1, 2, 3));
    }

    public function testIsInteger ()
    {
        $this->assertFalse(\Hoimi\Validator::isInteger('hoge'));
        $this->assertTrue(\Hoimi\Validator::isInteger(1234));
        $this->assertTrue(\Hoimi\Validator::isInteger('1234'));
    }

    public function testIsDouble ()
    {
        $this->assertFalse(\Hoimi\Validator::isDouble('abc'));
        $this->assertFalse(\Hoimi\Validator::isDouble('1.0a'));
        $this->assertTrue(\Hoimi\Validator::isDouble(0.1));
        $this->assertTrue(\Hoimi\Validator::isDouble('0.1'));
        $this->assertTrue(\Hoimi\Validator::isDouble('1'));
    }


    public function testIsDate ()
    {
        $this->assertFalse(\Hoimi\Validator::isDate('11/30 26:-1:61'));
        $this->assertFalse(\Hoimi\Validator::isDate('11/31'));
        $this->assertFalse(\Hoimi\Validator::isDate('hoge/fuga/piyo'));
        $this->assertTrue(\Hoimi\Validator::isDate('11/30 23:59:59'));
        $this->assertTrue(\Hoimi\Validator::isDate('2015/11/30'));
        $this->assertTrue(\Hoimi\Validator::isDate('9999/11/30'));
        $this->assertTrue(\Hoimi\Validator::isDate('1000/11/30'));
        $this->assertTrue(\Hoimi\Validator::isDate('11/30'));
        $this->assertTrue(\Hoimi\Validator::isDate('2000/02/29'));
        $this->assertFalse(\Hoimi\Validator::isDate('1999/02/29'));
        $this->assertTrue(\Hoimi\Validator::isDate('1999/02/28'));
    }
}