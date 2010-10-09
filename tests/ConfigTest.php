<?php
/**
 * @author Warnar Boekkooi
 * Created on: 8-10-10 14:24
 */ 
class ConfigTest extends PHPUnit_Framework_TestCase
{
	public function testConstructorEmpty()
	{
		$config = new \OAuth\Server\Config();

		try {
			$config->getStorage();
			$this->fail('expected exception');
		} catch (RuntimeException $e) {
			$this->assertEquals('No storage object has been set.', $e->getMessage());
		}

		$v = $config->getVersion();
		$this->assertEquals('1.0', $v);

		$r = $config->getInitiateRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\Initiate', $r);

		$r = $config->getAccessRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\Access', $r);

		$r = $config->getAccessTokenRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\AccessToken', $r);

		$r = $config->getAuthorizeRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\Authorize', $r);

		$u = $config->getHttpUtility();
		$this->assertInstanceOf('\OAuth\Server\Http\Utility', $u);

		$u = $config->getSignatureUtility();
		$this->assertInstanceOf('\OAuth\Server\Signature\Utility', $u);
	}

	public function testConstructorConfig()
	{
		// TODO: use some mock objects here
		$cfg = array(
			'storage' => new \OAuth\Server\Storage\Pdo\Storage(),
			'initiateRequest' => '\OAuth\Server\Request\Initiate',
			'authorizeRequest' => '\OAuth\Server\Request\Authorize',
			'accessTokenRequest' => '\OAuth\Server\Request\AccessToken',
			'accessRequest' => '\OAuth\Server\Request\Access'
		);

		$config = new \OAuth\Server\Config($cfg);

		$s = $config->getStorage();
		$this->assertInstanceOf('\OAuth\Server\Storage\Pdo\Storage', $s);		

		$v = $config->getVersion();
		$this->assertEquals('1.0', $v);

		$r = $config->getInitiateRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\Initiate', $r);

		$r = $config->getAccessRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\Access', $r);

		$r = $config->getAccessTokenRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\AccessToken', $r);

		$r = $config->getAuthorizeRequest();
		$this->assertInstanceOf('\OAuth\Server\Request\Authorize', $r);

		$u = $config->getHttpUtility();
		$this->assertInstanceOf('\OAuth\Server\Http\Utility', $u);

		$u = $config->getSignatureUtility();
		$this->assertInstanceOf('\OAuth\Server\Signature\Utility', $u);
	}

	public function testFailConstructor()
	{
		try {
			new \OAuth\Server\Config('crash');
			$this->fail('expected exception');
		} catch (InvalidArgumentException $e) {
			$this->assertEquals('Options must be a array or a instance of Zend_Config', $e->getMessage());
		}
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFailConstructorStorage()
	{
		new \OAuth\Server\Config(array('storage' => null));
		$this->fail('expected exception');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFailSetterStorage()
	{
		$config = new \OAuth\Server\Config();

		$config->setStorage('');
		$this->fail('expected exception');
	}

	/**
	 * @dataProvider dataRequestProvider
	 */
	public function testFailGetterSetterRequest($name)
	{
		$setter = 'set' . $name;
		$getter = 'get' . $name;

		$config = new \OAuth\Server\Config();

		$config->{$setter}('crash');
		try {
			$config->{$getter}();
			$this->fail('expected exception');
		} catch (RuntimeException $e) {
			$this->assertEquals(
				sprintf('`%s` must be a instance of \OAuth\Server\Request\RequestInterface', $name),
				$e->getMessage()
			);
		}
	}

	public function dataRequestProvider() {
		return array(
			array('AccessRequest'),
			array('InitiateRequest'),
			array('AccessTokenRequest'),
			array('AuthorizeRequest')
		);
	}
}
