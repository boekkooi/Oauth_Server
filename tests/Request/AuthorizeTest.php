<?php
namespace Tests\Request;

class AuthorizeTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var OAuth\Server\Request\RequestAbstract
	 */
	protected $request;
    
	protected function setUp()
	{
		$this->request = new \OAuth\Server\Request\Authorize();
	}

	protected function tearDown()
	{
		$this->request = null;
	}

    public function testAnalyzePost()
    {
        $config = new \OAuth\Server\Config();
        $this->request->setConfig($config);

        try {
            $params = $this->getRequestParams();
            $request = new \Zend_Controller_Request_HttpTestCase();
            $request->setMethod('Post')->setPost($params);
            $request->setPost($params);
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

        try {
            $params = $this->getRequestParams();
            $request = new \Zend_Controller_Request_HttpTestCase();
            $request->setHeader('Authorization', $config->getHttpUtility()->toAuthorizationHeader($params));
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
        $request->setMethod('Get')->setQuery($params);
        $this->request->analyze($request);
        $this->assertEquals($params, $this->request->getRawParams());
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
            'oauth_signature' => 'test',
            'oauth_token' => 'gotIt'
        );
    }
}
