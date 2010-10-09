<?php
namespace OAuth\Server;

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
class Server {
	/**
	 * @var OAuth\Server\Config
	 */
	protected $cfg = null;

    /**
     * Constructor.
     *
     * @param OAuth\Server\Config $configuration An OAuth\Server\Config instance.
     * @return void
	 */
    public function __construct(Config $configuration)
    {
		if (!($configuration instanceof Config)) {
			throw new \InvalidArgumentException('Argument `configuration` must be a instance of \OAuth\Server\Config.');
		}
		$this->cfg = $configuration;
    }

	public function initiate(\Zend_Controller_Request_Http $httpRequest) {
        // Create a server request and analyze the given httpRequest
		$request = $this->cfg->getInitiateRequest();
		$request->setConfig($this->cfg)->analyze($httpRequest);

		// Get storage adapter
		$storage = $this->cfg->getStorage();

        // Validate/get the consumer secret
		$consumerSecret = $this->getConsumerSecret($request);
		
        // Validate the request signature
        if (!$this->cfg->getSignatureUtility()->verifySignature($request->getRequestUri(), $request->getParams(), $consumerSecret)) {
            throw new \Exception('Invalid request. Invalid signature detected.');
        }

        // Get/create temporary credentials (identifier and shared-secret)
        $credentials = $storage->createTemporaryCredentials($request->getParam('consumer_key'), $request->getParam('oauth_callback'));
        if (empty($credentials) || !($credentials instanceof Storage\CredentialInterface)) {
            throw new \Exception('Unable to create temporary credentials');
        }

        // Generate the response body
        $body = array(
            'oauth_token' => $credentials->getToken(),
            'oauth_token_secret' => $credentials->getSecret(),
            'oauth_callback_confirmed' => true
        );
		$this->getPostResponse($body)->sendResponse();
        die;
	}

    public function authorize(\Zend_Controller_Request_Http $httpRequest, $user) {
        // Create a server request and analyze the given httpRequest
		$request = $this->cfg->getAuthorizeRequest();
		$request->setConfig($this->cfg)->analyze($httpRequest);

		// Get storage adapter
		$storage = $this->cfg->getStorage();
		
        // Get the temporary credentials identifier
        $token = $request->getParam('token');
		if (empty($token)) {
            throw new \Exception('Invalid request t');
		}

        // Get a oauth verification code.
        $verifyCode = $storage->createVerificationCode($token, $user);
        if (empty($verifyCode)) {
            throw new \Exception('Invalid request v');
        }

        // Get the callback URI
        $callbackUri = $storage->getCallbackUri($token);
        if (empty($callbackUri)) {
            return $verifyCode;
        }

        // Handle query strings in the callback uri
        $callbackUri = rtrim($callbackUri);
        if (parse_url($callbackUri, PHP_URL_QUERY) !== null) {
            $callbackUri = rtrim($callbackUri, '&') . '&';
        } else {
            $callbackUri = rtrim($callbackUri, '?') . '?';
        }

        // Add query string to the callback uri
        $callbackUri .= $this->cfg->getHttpUtility()->toEncodedQueryString(array('oauth_verifier' => $verifyCode, 'oauth_token' => $token));

        // Return verify code when there is no callback uri
        if (empty($callbackUri)) {
            return $verifyCode;
        }

        // Create a redirect response
        $response = new \Zend_Controller_Response_Http();
        $response->setRedirect($callbackUri, 303);
        $response->sendResponse();
        die;
    }

    /**
     * Token Credentials see: http://tools.ietf.org/html/draft-hammer-oauth-10#section-2.3
     * @return void
     */
    public function accessToken(\Zend_Controller_Request_Http $httpRequest) {
        // Create a server request and analyze the given httpRequest
		$request = $this->cfg->getAccessTokenRequest();
		$request->setConfig($this->cfg)->analyze($httpRequest);

		// Get storage adapter
		$storage = $this->cfg->getStorage();

		// Verify the access request
		$this->verifyAccessRequest($request, true);

        // Validate oauth verification code
        if (!$storage->isValidVerifierCode($request->getParam('oauth_verifier'), $request->getParam('token'), $request->getParam('consumer_key'))) {
            throw new \Exception('Invalid request. la');
        }

        // Get a token identifier and shared-secret
        // This will need to remove the temp creds.
        $credentials = $storage->createAccessCredentials($request->getParam('oauth_verifier'), $request->getParam('token'), $request->getParam('consumer_key'));
        if (empty($credentials) || !($credentials instanceof Storage\CredentialInterface)) {
            throw new \Exception('Unable to create credentials');
        }
		
		// Generate the response body
        $body = array(
            'oauth_token' => $credentials->getToken(),
            'oauth_token_secret' => $credentials->getSecret()
        );
		$this->getPostResponse($body)->sendResponse();
        die;
    }

	public function access(\Zend_Controller_Request_Http $httpRequest) {
        // Create a server request and analyze the given httpRequest
		$request = $this->cfg->getAccessRequest();
		$request->setConfig($this->cfg)->analyze($httpRequest);

		// Verify the access request
		$this->verifyAccessRequest($request);
		
		return true;
	}

	/**
	 * Verify the given request.
	 * This will verify the request signature and the request for replay attacks.
	 *
	 * @throws Exception Thrown when the request is invalid.
	 * @param Access $request An OAuth\Server\Request\Access instance.
	 * @return void
	 */
	protected function verifyAccessRequest(Request\Access $request, $temporary = false) {
		// Get storage adapter
		$storage = $this->cfg->getStorage();

		// Get the token secret
		if ($temporary) {
        	$tokenSecret = $storage->getTemporaryTokenSecret($request->getParam('token'), $request->getParam('consumer_key'));
		} else {
        	$tokenSecret = $storage->getTokenSecret($request->getParam('token'), $request->getParam('consumer_key'));
		}
		if (empty($tokenSecret)) {
            throw new \Exception('Invalid request. No or invalid token.');
		}

		// Get the consumer secret
		$consumerSecret = $this->getConsumerSecret($request);

        // Validate the request signature
        if (!$this->cfg->getSignatureUtility()->verifySignature($request->getRequestUri(), $request->getParams(), $consumerSecret, $tokenSecret)) {
            throw new \Exception('Invalid request. Invalid signature detected.');
        }

		// Validate that the request is the first one (see: http://tools.ietf.org/html/draft-hammer-oauth-10#section-3.3)
		if (!$storage->isValidTokenRequest($request->getParam('token'), $request->getParam('oauth_nonce'), $request->getParam('oauth_timestamp'))) {
            throw new \Exception('Invalid request. Possible replay attack.');
		}
	}

	/**
	 * Get the consumer secret based on the consumer key in the request.
	 *
	 * @throws Exception Thrown when the consumer is unknown or not authorized.
	 * @param RequestAbstract $request
	 * @return string
	 */
	protected function getConsumerSecret(Request\RequestAbstract $request)
	{
        // Get the consumer secret based on the key
        $consumerSecret = $this->cfg->getStorage()->getCustomerSecret($request->getParam('consumer_key'));
		if (empty($consumerSecret)) {
            throw new \Exception('Invalid request. No or invalid consumer key.');
		}
		return $consumerSecret;
	}

	protected function getPostResponse(array $params) {
        $body = $this->cfg->getHttpUtility()->toEncodedQueryString($params);

        // Create the response
        $response = new \Zend_Controller_Response_Http();
        $response->setHttpResponseCode(200)->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $response->setBody($body);
        return $response;
	}
}
