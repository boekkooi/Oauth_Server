<?php
namespace Request;

/**
 * @author Warnar Boekkooi
 * Created on: 8-10-10 16:57
 */ 
class RequestAbstractTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var OAuth\Server\Request\RequestAbstract
	 */
	protected $request;

	protected function setUp()
	{
		// TODO create mock object
		$this->request = new \OAuth\Server\Request\Initiate();
	}

	protected function tearDown()
	{
		$this->request = null;
	}

	public function testRawParams()
    {
        $this->assertEquals(array(), $this->request->getRawParams());

        $params = array();
		$this->request->setRawParams($params);
        $this->assertEquals($params, $this->request->getRawParams());

        $params = array('abc' => 'abc');
		$this->request->setRawParams($params);
        $this->assertEquals($params, $this->request->getRawParams());

        $params = array('a', 'b', 'c');
		$this->request->setRawParams($params);
        $this->assertEquals($params, $this->request->getRawParams());
	}

	public function testGetParams()
    {
        $this->assertEquals(array(), $this->request->getParams());

        $params = array();
		$this->request->setRawParams($params);
        $this->assertEquals($params, $this->request->getParams());

        $params = array('abc' => 'abc', 'oauth_test' => 'valid', 'a');
		$this->request->setRawParams($params);
        $this->assertEquals(array('oauth_test' => 'valid'), $this->request->getParams());
	}

	public function testGetParam()
    {
        $this->assertEquals(null, $this->request->getParam('test'));

		$this->request->setRawParams(array('oauth_test' => 'valid'));
        $this->assertEquals('valid', $this->request->getParam('test'));
        $this->assertEquals('valid', $this->request->getParam('oauth_test'));

        $this->assertEquals(null, $this->request->getParam('test1'));
        $this->assertEquals(null, $this->request->getParam('oauth_test1'));

        // TODO Is this expected
		$this->request->setRawParams(array('oauth_' => 'valid'));
        $this->assertEquals('valid', $this->request->getParam(''));
	}

    public function testValidateArray()
    {
		$reflection = new \ReflectionMethod('\OAuth\Server\Request\RequestAbstract', 'validateArray');
		$reflection->setAccessible(true);

        $arr = array();
        try {
            $this->assertFalse($reflection->invoke($this->request, $arr));
			$this->fail('expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('No configuration has been set', $e->getMessage());
        }

        $this->request->setConfig(new \OAuth\Server\Config());

        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_consumer_key'] = 'a';
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_signature_method'] = 'a';
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_signature_method'] = 'PLAINTEXT';
        $this->assertTrue($reflection->invoke($this->request, $arr));

        $arr['oauth_signature_method'] = 'HMAC-SHA1';
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_nonce'] = 'a';
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_timestamp'] = 'a';
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_timestamp'] = 1;
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_timestamp'] = strtotime('-2 day');
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_timestamp'] = strtotime('+2 day');
        $this->assertFalse($reflection->invoke($this->request, $arr));

        $arr['oauth_timestamp'] = time();
        $this->assertTrue($reflection->invoke($this->request, $arr));
    }

	public function testAnalyze()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);

        try {
            $request = new \Zend_Controller_Request_HttpTestCase();
		    $this->request->analyze($request);
			$this->fail('expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Invalid or unknown type of request given.', $e->getMessage());
        }
	}

    public function testAnalyzeHeader()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);

        $params = $this->getRequestParams();
        $request = new \Zend_Controller_Request_HttpTestCase();
        $request->setHeader('Authorization', $config->getHttpUtility()->toAuthorizationHeader($params));
        $this->request->analyze($request);
        $this->assertEquals($params, $this->request->getRawParams());
	}

    public function testFailAnalyzeHeader()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);
        
        $request = new \Zend_Controller_Request_HttpTestCase();
        $request->setHeader('Authorization', '');
        try {
            $this->request->analyze($request);
			$this->fail('expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Invalid or unknown type of request given.', $e->getMessage());
        }
    }

    public function testAnalyzePost()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);

        $params = $this->getRequestParams();
        $request = new \Zend_Controller_Request_HttpTestCase();
        $request->setMethod('POST')->setPost($params);
        $this->request->analyze($request);
        $this->assertEquals($params, $this->request->getRawParams());
	}

    public function testFailAnalyzePost()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);
        
        $request = new \Zend_Controller_Request_HttpTestCase();
        $request->setMethod('POST')->setPost(array());
        try {
            $this->request->analyze($request);
			$this->fail('expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Invalid or unknown type of request given.', $e->getMessage());
        }
    }

    public function testAnalyzeQuery()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);

        $params = $this->getRequestParams();
        $request = new \Zend_Controller_Request_HttpTestCase();
        $request->setQuery($params);
        $this->request->analyze($request);
        $this->assertEquals($params, $this->request->getRawParams());
	}

    public function testFailAnalyzeQuery()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);
        
        $request = new \Zend_Controller_Request_HttpTestCase();
        $request->setQuery(array('a'));
        try {
            $this->request->analyze($request);
			$this->fail('expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Invalid or unknown type of request given.', $e->getMessage());
        }
    }

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFailAnalyze()
    {
		$this->request->analyze(null);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFailRawParamsEmpty()
    {
		$this->request->setRawParams(null);
	}

    protected function getRequestParams() {
        return array (
            'OAuth realm' => '',
            'oauth_consumer_key' => '1234567890',
            'oauth_nonce' => 'e807f1fcf82d132f9bb018ca6738a19f',
            'oauth_timestamp' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
            'oauth_callback' => 'http://www.example.com/local',
            'oauth_signature' => 'test'
        );
    }
}
