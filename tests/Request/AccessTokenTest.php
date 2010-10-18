<?php
namespace Tests\Request;

class AccessTokenTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var OAuth\Server\Request\RequestAbstract
	 */
	protected $request;
    
	protected function setUp()
	{
		$this->request = new \Tests\Mock\Request\AccessToken();
	}

	protected function tearDown()
	{
		$this->request = null;
	}

    public function testValidateArray()
    {
        $arr = array();
        try {
            $this->assertFalse($this->request->validateArray($arr));
			$this->fail('expected exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('No configuration has been set', $e->getMessage());
        }

        $this->request->setConfig(new \OAuth\Server\Config());

        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_consumer_key'] = 'a';
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_signature_method'] = 'a';
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_signature_method'] = 'PLAINTEXT';
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_signature_method'] = 'HMAC-SHA1';
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_nonce'] = 'a';
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_timestamp'] = 'a';
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_timestamp'] = 1;
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_timestamp'] = strtotime('-2 day');
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_timestamp'] = strtotime('+2 day');
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_timestamp'] = time();
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_token'] = 'gotit';
        $this->assertFalse($this->request->validateArray($arr));

        $arr['oauth_verifier'] = 'got';
        $this->assertTrue($this->request->validateArray($arr));
    }
}
