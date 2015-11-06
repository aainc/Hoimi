<?php
namespace Hoimi;
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Hoimi\Router
     */
    private $target = null;

    protected function setUp()
    {
        $this->target = new Router(array(
            '/' => 'Hoimi\Dummy\Index',
            '/hoge' => 'Hoimi\Dummy\NoClass',
            '/fuga' => 'Hoimi\Dummy\NotImplements',
            '/piyo/post' => 'Hoimi\Dummy\Nest\PostRequest',
            '/piyo/{dummy_id}/hoge/{xyzzy}' => 'Hoimi\Dummy\Index',
        ));
    }

    public function testOkGet()
    {
        list($action, $method) = $this->target->run(new Request(array(
            'REQUEST_URI' => '/',
            'REQUEST_METHOD' => 'GET',
        ), array()));
        $this->assertInstanceOf('Hoimi\Dummy\Index', $action);
        $this->assertEquals('get', $method);
    }

    public function testOkPost()
    {
        list($action, $method) = $this->target->run(new Request(array(
            'REQUEST_URI' => '/piyo/post',
            'REQUEST_METHOD' => 'POST',
        ), array()));
        $this->assertInstanceOf('Hoimi\Dummy\Nest\PostRequest', $action);
        $this->assertEquals('post', $method);
    }

    /**
     * @expectedException \Hoimi\Exception\NotFoundException
     */
    public function testNoClass()
    {
        list($action, $method) = $this->target->run(new Request(array(
            'REQUEST_URI' => '/hoge',
            'REQUEST_METHOD' => 'GET',
        ), array()));
    }

    /**
     * @expectedException \Hoimi\Exception\NotFoundException
     */
    public function testNoRequest()
    {
        list($action, $method) = $this->target->run(new Request(array(
            'REQUEST_URI' => '/piyo/post',
            'REQUEST_METHOD' => 'GET',
        ), array()));
    }

    /**
     * @expectedException \Hoimi\Exception\NotFoundException
     */
    public function testNoImplements()
    {
        list($action, $method) = $this->target->run(new Request(array(
            'REQUEST_URI' => '/fuga',
            'REQUEST_METHOD' => 'GET',
        ), array()));
    }

    public function testRegEx()
    {
        $request = new Request(array(
            'REQUEST_URI' => '/piyo/1234/hoge/5678' ,
            'REQUEST_METHOD' => 'GET',
        ), array());
        list($action, $method) = $this->target->run($request);
        $this->assertInstanceOf('Hoimi\Dummy\Index', $action);
        $this->assertSame('get', $method);
        $this->assertSame('1234', $request->getUrlParameter('dummy_id'));
        $this->assertSame('5678', $request->getUrlParameter('xyzzy'));
    }

    public function testRegEx2()
    {
        $request = new Request(array(
            'REQUEST_URI' => '/piyo/{dummy_id}/hoge/{dummy_id}' ,
            'REQUEST_METHOD' => 'GET',
        ), array());
        list($action, $method) = $this->target->run($request);
        $this->assertInstanceOf('Hoimi\Dummy\Index', $action);
        $this->assertSame('get', $method);
        $this->assertSame('{dummy_id}', $request->getUrlParameter('dummy_id'));
        $this->assertSame('{dummy_id}', $request->getUrlParameter('xyzzy'));
    }
}

namespace Hoimi\Dummy;
class Index extends \Hoimi\BaseAction
{
    public function get()
    {
    }
}

class NoImplements
{
    public function get()
    {
    }

    public function post()
    {
    }

    public function delete()
    {
    }
}

namespace Hoimi\Dummy\Nest;
class PostRequest extends \Hoimi\BaseAction
{
    public function post()
    {
    }
}
