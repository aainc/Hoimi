<?php
namespace Hoimi;

class BatchRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BatchRequest
     */
    private $target = null;

    protected function setUp()
    {
        $this->target = new BatchRequest();
        $this->target->setConfig(new Config());
        Router::getInstance()->setRoutes(array(
            '/hoge' => 'Hoimi\dummy\Batch1',
            '/fuga' => 'Hoimi\dummy\Batch2',
        ));
    }

    public function testPost ()
    {
        $this->target->setRequest(new Request(array(),array(
            'batch' => json_encode(array(
                'requests'  => array(
                    array('url' => '/hoge', 'method' => 'post','params' => array()),
                    array('url' => '/fuga', 'method' => 'post','params' => array()),
                ),
            )))));
        $result = $this->target->post();
        $this->assertSame(array(
            'data' => array(
                array('request' => '/hoge', 'method' => 'post', 'error' => null, 'result' => '{"result":"ok"}'),
                array('request' => '/fuga', 'method' => 'post', 'error' => null, 'result' => '{"result":"ok"}'),
            )
        ), $result->getData());
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testNoRequest ()
    {
        $this->target->setRequest(new Request(array(),array()));
        $result = $this->target->post();
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testInvalidRequest ()
    {
        $this->target->setRequest(new Request(array(),array(
            'batch' => 'hoge'
        )));
        $result = $this->target->post();
    }

    public function testInvalid ()
    {
        $this->target->setRequest(new Request(array(),array(
            'batch' => json_encode(array (
                'requests'  => array(
                    array('url' => '/hoge', 'method' => null,'params' => array()),
                    array('url' => null, 'method' => 'post','params' => array()),
                )
            )),
        )));
        $result = $this->target->post();
        $this->assertSame(array(
            'data' => array(
                array('request' => '/hoge', 'method' => null, 'error' => 'invalid argument. url or method is null', 'result' => null),
                array('request' => null, 'method' => 'post', 'error' => 'invalid argument. url or method is null', 'result' => null),
            )), $result->getData());
    }
}


namespace Hoimi\dummy;

use Hoimi\BaseAction;
use Hoimi\Response\Json;

class Batch1 extends BaseAction {
    public function post() {
        return new Json(array('result' => 'ok'));
    }
}
class Batch2 extends BaseAction {
    public function post() {
        return new Json(array('result' => 'ok'));
    }
}
