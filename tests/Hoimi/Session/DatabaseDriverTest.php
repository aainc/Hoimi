<?php
namespace Hoimi\Session;

use Hoimi\Config;
interface StatementDummy {
    public function bind_result();
    public function execute();
    public function bind_param();
    public function close();
    public function fetch();
}
class DatabaseDriverTest extends \PHPUnit_Framework_TestCase
{
    private $connection = null;
    private $statement = null;
    /**
     * @var \Hoimi\Session\DatabaseDriver
     */
    private $target = null;

    public function setUp()
    {
        $this->connection = \Phake::mock('\mysqli');
        $this->statement = \Phake::mock('\Hoimi\Session\StatementDummy');
        $this->target = \Phake::partialMock('Hoimi\Session\DatabaseDriver', new Config());
        $this->target->setConnection($this->connection);
    }

    public function testReadOkResult()
    {
        \Phake::when($this->connection)->prepare(\Phake::anyParameters())->thenReturn($this->statement);
        \Phake::when($this->statement)->fetch()->thenReturn(true);
        $this->assertSame(null, $this->target->read('1234'));
        \Phake::verify($this->statement)->bind_param('s', '1234');
        \Phake::verify($this->statement)->close();
    }

    public function testReadOkNothing()
    {
        \Phake::when($this->connection)->prepare(\Phake::anyParameters())->thenReturn($this->statement);
        \Phake::when($this->statement)->fetch()->thenReturn(false);
        $this->assertSame('', $this->target->read('1234'));
        \Phake::verify($this->statement)->bind_param('s', '1234');
        \Phake::verify($this->statement)->close();
    }

    public function testSaveInsert()
    {
        \Phake::when($this->target)->read('1234')->thenReturn('');
        \Phake::when($this->connection)->prepare("INSERT INTO session_store(session_id, session_data, updated_at) VALUES (?, ?, ?)")->thenReturn($this->statement);
        $this->target->write('1234', 'hoge');
        \Phake::verify($this->connection)->prepare("INSERT INTO session_store(session_id, session_data, updated_at) VALUES (?, ?, ?)");
        \Phake::verify($this->statement)->bind_param('ssd', '1234', 'hoge', $this->anything());
        \Phake::verify($this->statement)->close();
    }

    public function testSaveUpdate()
    {

        \Phake::when($this->target)->read('1234')->thenReturn('hoge');
        \Phake::when($this->connection)->prepare("UPDATE session_store SET session_data=?, updated_at=? WHERE session_id=?")->thenReturn($this->statement);
        $this->target->write('1234', 'fuga');
        \Phake::verify($this->connection)->prepare("UPDATE session_store SET session_data=?, updated_at=? WHERE session_id=?");
        \Phake::verify($this->statement)->bind_param('sds', 'fuga', $this->anything(), '1234');
        \Phake::verify($this->statement)->close();
    }
}