<?php
namespace Tests\Signature;

/**
 * @author Warnar Boekkooi
 * Created on: 8-10-10 16:13
 */ 
class UtilityTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var OAuth\Server\Signature\Utility
	 */
	protected $util;

	protected function setUp()
	{
		$this->util = new \Tests\Signature\Utility();
	}

	protected function tearDown()
	{
		$this->util = null;
	}

	public function validMethodProviders() {
		return array(
			array('HMAC-SHA1', array('OAuth\Server\Signature\Hmac', 'SHA1')),
			array('HMAC-SHA256', array('OAuth\Server\Signature\Hmac', 'SHA256')),
			array('PLAINTEXT', array('OAuth\Server\Signature\Plaintext', null))
		);
	}

	public function invalidMethodProviders() {
		return array(
			array(null),
			array(''),
			array(' '),
			array(PHP_EOL),
			array('HMAC-SHA11'),
			array('HMAC1-SHA256'),
			array('SMS-PLAINTEXT')
		);
	}

	/**
	 * @dataProvider validMethodProviders
	 */
	public function testIsValidSignatureMethod($method)
	{
		$this->assertTrue($this->util->isValidSignatureMethod($method));
	}

	/**
	 * @dataProvider invalidMethodProviders
	 */
	public function testFailIsValidSignatureMethod($method)
	{
		$this->assertFalse($this->util->isValidSignatureMethod($method));
	}

	/**
	 * @dataProvider validMethodProviders
	 */
	public function testGetSignatureInfo($method, $expected)
	{		
		$this->assertEquals($expected, $this->util->getSignatureInfo($method));
	}

	/**
	 * @dataProvider invalidMethodProviders
	 */
	public function testFailGetSignatureInfo($method)
	{
		try {
			$this->util->getSignatureInfo($method);
			$this->fail('expected exception');
		} catch (\RuntimeException $e) {
			$expected = 'Unsupported signature method: ' . $method . '. Supported are HMAC-SHA1, PLAINTEXT and HMAC-SHA256';
			$this->assertEquals($expected, $e->getMessage());
		}
	}
}
