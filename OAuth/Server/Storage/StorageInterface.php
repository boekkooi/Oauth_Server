<?php
namespace OAuth\Server\Storage;

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
interface StorageInterface
{
    /**
     * Get the consumer secret based on the given consumer key.
     *
     * @abstract
     * @param string $consumerKey A client credentials identifier.
     * @return string|null The consumer secret or NULL when the consumer is unknown.
     */
    public function getCustomerSecret($consumerKey);
    
    /**
     * Get temporary credentials for the first part of the authentication process
     *
     * @abstract
     * @param string $consumerKey A client credentials identifier.
     * @return void
     */
    public function createTemporaryCredentials($consumerKey, $callbackUri);

    /**
     * Get a verification code for the given temporary token.
     *
     * @abstract
     * @param string $temporaryToken A temporary credentials identifier.
     * @param mixed $user The user that will be bound to the verification code.
     * @return void
     */
    public function createVerificationCode($temporaryToken, $user);

    /**
     * Get the callback uri of the given temporary token.
     *
     * @abstract
     * @param string $temporaryToken A temporary credentials identifier.
     * @return string|null
     */
    public function getCallbackUri($temporaryToken);

    /**
     * Validate if the given verification code is correct in combination with the temporary token and the consumer key.
     *
     * @abstract
     * @param string $verifierCode A verification code.
     * @param string $temporaryToken A temporary credentials identifier.
     * @param string $consumerKey A client credentials identifier.
     * @return boolean True if the verification code is valid else FALSE.
     */
    public function isValidVerifierCode($verifierCode, $temporaryToken, $consumerKey);

    /**
     * Get temporary credentials for the first part of the authentication process
     *
     * @abstract
     * @param string $consumerKey A client credentials identifier.
     * @return void
     */
    public function createAccessCredentials($verifierCode, $temporaryToken, $consumerKey);

    /**
     * Get the temporary token secret based on the given temporary token.
     *
     * @abstract
     * @param string $token A temporary token.
     * @return string|null The temporary token secret secret or NULL when the temporary token is unknown.
     */
    public function getTemporaryTokenSecret($token);

    /**
     * Get the token secret based on the given token.
     *
     * @abstract
     * @param string $token A token.
     * @return string|null The token secret secret or NULL when the token is unknown.
     */
    public function getTokenSecret($token, $consumerKey);

	/**
	 * Validate that the given token request has not happened before.
	 * See http://tools.ietf.org/html/draft-hammer-oauth-10#section-3.3 for more information.
	 *
	 * @abstract
	 * @param string $token A token.
	 * @param string $nonce A Nonce.
	 * @param int $timestamp A timestamp since the Unix Epoch.
	 * @return bool
	 */
	public function isValidTokenRequest($token, $nonce, $timestamp);
}
