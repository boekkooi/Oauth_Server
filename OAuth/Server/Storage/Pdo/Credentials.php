<?php
namespace OAuth\Server\Storage\Pdo;

/**
 * @package OAuth_Server
 * @author Warnar Boekkooi
 *
 * The MIT License
 *
 * Copyright (c) 2010 Warnar Boekkooi
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the \"Software\"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
*/
class Credentials implements \OAuth\Server\Storage\CredentialInterface {
	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $secret;

	/**
	 * Constructor
	 *
	 * @throws InvalidArgumentException
	 * @param string $token
	 * @param string $secret
	 * @return void
	 */
	public function __construct($token, $secret) {
		if (empty($token) || !is_string($token)) {
			throw new \InvalidArgumentException('token must be a string and may not be empty.');
		}
		if (empty($secret) || !is_string($secret)) {
			throw new \InvalidArgumentException('secret must be a string and may not be empty.');
		}

		$this->token = $token;
		$this->secret = $secret;
	}

	/**
	 * Get the secret
	 *
	 * @abstract
	 * @return string The secret.
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * Get the token/identifier.
	 *
	 * @abstract
	 * @return string The token/identifier.
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Generate 'random' credentials.
	 *
	 * @static
	 * @return OAuth\Server\Storage\Pdo\Credentials The generated credentials instance.
	 */
	public static function generateCredentials() {
		// Generate token
		$token = static::generateString() . dechex(microtime(true));

		// Generate secret
		$secret = static::generateString();

		// Create and return credentials
		return new Credentials($token, $secret);
	}

	/**
	 * Generate a pseudo random string
	 * @static
	 * @return string
	 */
	public static function generateString() {
		do {
			$bytes = openssl_random_pseudo_bytes(8, $isStrong);
		} while(!$isStrong);
		return bin2hex($bytes);
	}
}