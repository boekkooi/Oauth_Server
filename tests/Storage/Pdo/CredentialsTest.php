<?php
namespace Tests\Signature;

/**
 * @author Warnar Boekkooi
 */
class CredentialsTest extends \PHPUnit_Framework_TestCase {
    public function testConstructor() {
        $credentials = new \OAuth\Server\Storage\Pdo\Credentials('token', 'secret');
        $this->assertEquals('token', $credentials->getToken());
        $this->assertEquals('secret', $credentials->getSecret());
    }

    public function testConstructorEmpty() {
        try {
            new \OAuth\Server\Storage\Pdo\Credentials(null, null);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals('token must be a string and may not be empty.', $e->getMessage());
        }

        try {
            new \OAuth\Server\Storage\Pdo\Credentials(10, null);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals('token must be a string and may not be empty.', $e->getMessage());
        }

        try {
            new \OAuth\Server\Storage\Pdo\Credentials('token', null);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals('secret must be a string and may not be empty.', $e->getMessage());
        }

        try {
            new \OAuth\Server\Storage\Pdo\Credentials('token', 99);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals('secret must be a string and may not be empty.', $e->getMessage());
        }
    }

    public function testGenerate() {
        $credentials = \OAuth\Server\Storage\Pdo\Credentials::generateCredentials();
        $this->assertType('OAuth\Server\Storage\Pdo\Credentials', $credentials);
    }

}
