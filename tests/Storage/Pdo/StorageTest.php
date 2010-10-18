<?php
namespace Tests\Signature;

class StorageTest extends \PHPUnit_Extensions_Database_TestCase {
    protected $pdo;

    public function __construct() {
        $this->pdo = new \PDO('mysql:host=localhost;dbname=oauth', 'test', '');

        $sql = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Fixture/oauth_test.sql');
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = $this->pdo->prepare($statement);
            $statement->execute();
            $statement->closeCursor();
        }
    }

    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, 'testdb');
    }

    protected function getDataSet()
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        return $dataSet;
    }

    public function testConstructor() {
        new \OAuth\Server\Storage\Pdo\Storage(null);

        $pdo = $this->getConnection()->getConnection();
        $instance = new \OAuth\Server\Storage\Pdo\Storage($pdo);
        $this->assertEquals($pdo, $instance->getPdo());
    }

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
    public function testFailConstructor() {
        new \OAuth\Server\Storage\Pdo\Storage('test');
    }

    public function testFailGetPdo() {
        try {
           $instance = new \OAuth\Server\Storage\Pdo\Storage(null);
           $instance->getPdo();
           $this->fail('expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('No PDO object has been set.', $e->getMessage());
        }
    }

    public function testGetCustomerSecret() {
        $pdo = $this->getConnection()->getConnection();
        $instance = new \OAuth\Server\Storage\Pdo\Storage($pdo);
        $this->assertNull($instance->getCustomerSecret('invalid'));
    }

    public function testIsValidTokenRequest() {
        $pdo = $this->getConnection()->getConnection();
        $instance = new \OAuth\Server\Storage\Pdo\Storage($pdo);

        $time = time();
        $this->assertTrue($instance->isValidTokenRequest('a', 'b', $time));
        $this->assertTrue($instance->isValidTokenRequest('a', 'a', $time));
        $this->assertTrue($instance->isValidTokenRequest('b', 'b', $time));
    }

    public function testFailIsValidTokenRequest() {
        $pdo = $this->getConnection()->getConnection();
        $instance = new \OAuth\Server\Storage\Pdo\Storage($pdo);

        $time = time();
        $this->assertTrue($instance->isValidTokenRequest('c', 'c', $time));
        $this->assertFalse($instance->isValidTokenRequest('c', 'c', $time));
    }
}
