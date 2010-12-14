<?php
namespace Tests\Signature;

class StorageTest extends \PHPUnit_Extensions_Database_TestCase {
    protected $pdo;

    public function __construct() {
        $this->pdo = new \PDO('mysql:host=localhost;dbname=oauth', 'root', '');

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

    public function testIsValidRequest() {
        $pdo = $this->getConnection()->getConnection();
        $instance = new \OAuth\Server\Storage\Pdo\Storage($pdo);

		$request = new \Tests\Mock\Request\RequestAbstract();
		$params = array(
		   'oauth_timestamp' => time(),
		   'oauth_consumer_key' => 'a',
		   'oauth_nonce' => 'a',
	   );

		$request->setRawParams($params);
        $this->assertTrue($instance->isValidRequest($request));

		$params['oauth_nonce'] = 'b';
		$request->setRawParams($params);
        $this->assertTrue($instance->isValidRequest($request));

		$params['oauth_consumer_key'] = 'b';
		$request->setRawParams($params);
        $this->assertTrue($instance->isValidRequest($request));
    }

    public function testFailIsValidTokenRequest() {
        $pdo = $this->getConnection()->getConnection();
        $instance = new \OAuth\Server\Storage\Pdo\Storage($pdo);

		$request = new \Tests\Mock\Request\RequestAbstract();
		$request->setRawParams(array(
		   'oauth_timestamp' => time(),
		   'oauth_consumer_key' => 'a',
		   'oauth_nonce' => 'new',
	   ));

        $this->assertTrue($instance->isValidRequest($request));
        $this->assertFalse($instance->isValidRequest($request));
    }
}
