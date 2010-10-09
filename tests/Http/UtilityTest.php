<?php
namespace Http;

/**
 * @author Warnar Boekkooi
 * Created on: 8-10-10 15:42
 */ 
class UtilityTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var OAuth\Server\Http\Utility
	 */
	protected $util;

	protected function setUp()
	{
		$this->util = new \OAuth\Server\Http\Utility();
	}

	protected function tearDown()
	{
		$this->util = null;
	}

	public function testParseAuthorizationHeader()
	{
		$expected = array (
			'oauth_consumer_key' => 'dpf43f3p2l4k3l03',
			'oauth_signature_method'=> 'HMAC-SHA1',
			'oauth_timestamp' => '137131200'
		);
		$result = $this->util->parseAuthorizationHeader('oauth_consumer_key="dpf43f3p2l4k3l03",oauth_signature_method="HMAC-SHA1",oauth_timestamp="137131200"');
		$this->assertEquals($expected, $result);
	}

	public function testParseAuthorizationHeaderEmpty()
	{
		$expected = array ();
		$result = $this->util->parseAuthorizationHeader('');
		$this->assertEquals($expected, $result);
	}
}
