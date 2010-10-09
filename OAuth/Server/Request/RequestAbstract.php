<?php
namespace OAuth\Server\Request;

use Zend_Oauth;

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
class RequestAbstract implements RequestInterface
{
    protected $requestUrl = null;

    protected $rawParams = array();

    protected $params = null;

	/**
	 * Three request schemes are defined by OAuth, of which passing
     * all OAuth parameters by Header is preferred. The other two are
     * POST Body and Query String.
	 */
	protected $requestScheme = null;

	/**
	 * @var OAuth\Server\Config
	 */
    protected $cfg = null;

	/**
	 * Set the server configuration.
	 *
	 * @param OAuth\Server\Config $config
	 * @return RequestAbstract
	 */
	public function setConfig(\OAuth\Server\Config $config) {
		$this->cfg = $config;
		return $this;
	}

	/**
	 * Check what kind of request was made to the oauth server.
	 * @return void
	 */
	public function analyze(\Zend_Controller_Request_Http $request)
	{
		if (!($request instanceof \Zend_Controller_Request_Http)) {
			throw new \InvalidArgumentException('`request` must be a instance of `Zend_Controller_Request_Http`');
		}

		// Set the requested url
		$this->requestUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getRequestUri();

		// Check if the request was based on the 'Authorization' header
		$header = $request->getHeader('Authorization', false);
		if ($header !== false) {
			// Parse the 'Authorization' header
			$header = $this->cfg->getHttpUtility()->parseAuthorizationHeader($header);

			// Validate if the given array is a valid oauth array.
			if ($this->validateArray($header)) {
                $this->setRawParams($header);
				$this->requestScheme = Zend_Oauth::REQUEST_SCHEME_HEADER;
				return;
			}
		}

		// Check if the request was based a post request
		if($request->isPost()) {

			// Validate if the given array is a valid oauth array.
			$post = $request->getPost();
			if ($this->validateArray($post)) {
                $this->setRawParams($post);
				$this->requestScheme = Zend_Oauth::REQUEST_SCHEME_POSTBODY;
				return;
			}
		}

		// Check if the request was based a get request
		$query = $request->getQuery(); // Should i use Zend_Oauth_Http_Utility::parseQueryString

		// Validate if the given array is a valid oauth array.
		if ($this->validateArray($query)) {
            $this->setRawParams($query);
			$this->requestScheme = Zend_Oauth::REQUEST_SCHEME_QUERYSTRING;
			return;
		}

		// Sorry we can't handle this right now
		throw new \RuntimeException('Invalid or unknown type of request given.');
	}

    /**
     * Get a oauth parameter.
     *
     * @param string $name The name of the parameter
     * @param mixed $default The value returned when the param is not existing.
     * @return string The param value
     */
    public function getParam($name, $default = null) {
        $params = $this->getParams();
        return isset($params[$name]) ? $params[$name] : (isset($params['oauth_' . $name]) ? $params['oauth_' . $name] : $default);
    }

    /**
     * Get the oauth parameters
     * @return string
     */
    public function getParams() {
        if ($this->params === null) {
            $this->params = array_intersect_key($this->getRawParams(), array_flip(array_filter(array_keys($this->getRawParams()), function($k) {
                return strpos($k, 'oauth_') === 0;
            })));
        }
        
        return $this->params;
    }

    /**
     * Set the raw request parameters.
     *
     * @param array $params The raw request params.
     * @return self
     */
    public function setRawParams(array $params) {
        $this->rawParams = $params;
        $this->params = null;
        
        return $this;
    }

    /**
     * Get the raw request parameters.
     *
     * @param array $params The raw request params.
     * @return void
     */
    public function getRawParams() {
        return $this->rawParams;
    }

	/**
	 * Validate if the expected `oauth_` keys are available.
	 *
	 * @param array $array
	 * @return bool The needed keys are available.
	 */
	protected function validateArray(array $array)
	{
        if ($this->cfg === null) {
            throw new \RuntimeException('No configuration has been set');
        }

		// Validate request (http://tools.ietf.org/html/draft-hammer-oauth-10#section-3.1)
		if (empty($array) ||
			empty($array['oauth_consumer_key']) ||
			empty($array['oauth_signature_method']) ||
			!$this->cfg->getSignatureUtility()->isValidSignatureMethod($array['oauth_signature_method']) ||
			!empty($array['oauth_version']) && $array['oauth_version'] !== "1.0") {
			return false;
		}

		if($array['oauth_signature_method'] !== "PLAINTEXT") {
			// Check the nonce
			if (empty($array['oauth_nonce']) || empty($array['oauth_timestamp'])) {
				return false;
			}

			// Check age of the oauth_timestamp
			$t = $array['oauth_timestamp'];
			if (!is_numeric($t) || intval($t) < strtotime('-1 day') || intval($t) > strtotime('+1 day')) {
				return false;
			}
		}

		return true;
	}

	public function getRequestUri() {
		return $this->requestUrl;
	}
}
