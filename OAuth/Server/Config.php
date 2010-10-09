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
class Config {
	/**
     * OAuth Version; This defaults to 1.0 - Must not be changed!
     *
     * @var string
     */
    protected $version = '1.0';

	/**
	 * Oauth server storage instance use tot retrieve and create data.
	 *
	 * @var OAuth\Server\Storage\StorageInterface
	 */
	protected $storage = null;

	/**
	 * @var OAuth\Server\Http\Utility
	 */
	protected $httpUtility;

	/**
	 * @var OAuth\Server\Signature\Utility
	 */
	protected $signatureUtility;

	/**
	 * @var OAuth\Server\Request\AccessInterface|string
	 */
	protected $accessRequest = null;

	/**
	 * @var OAuth\Server\Request\RequestInterface|string
	 */
	protected $initiateRequest;

	/**
	 * @var OAuth\Server\Request\AuthorizeInterface|string
	 */
	protected $authorizeRequest;

	/**
	 * @var OAuth\Server\Request\AccessTokenInterface|string
	 */
	protected $accessTokenRequest;

	/**
     * Constructor; create a new object with an optional array|Zend_Config
     * instance containing initialising options.
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (!is_null($options)) {
            if ($options instanceof Zend_Config) {
                $options = $options->toArray();
            } elseif (!is_array($options)) {
				throw new \InvalidArgumentException('Options must be a array or a instance of Zend_Config');
			}
            $this->setOptions($options);
        }
    }

	/**
     * Parse option array or Zend_Config instance and setup options using their
     * relevant mutators.
     *
     * @param  array|Zend_Config $options
     * @return self
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'storage':
                    $this->setStorage($value);
                    break;
                case 'initiateRequest':
                    $this->setInitiateRequest($value);
                    break;
                case 'authorizeRequest':
                    $this->setAuthorizeRequest($value);
                    break;
                case 'accessTokenRequest':
                    $this->setAccessTokenRequest($value);
                    break;
                case 'accessRequest':
                    $this->setAccessRequest($value);
                    break;
            }
        }

        return $this;
    }

	/**
	 * Get the OAuth Version.
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Get the HTTP utility.
	 *
	 * @return OAuth\Server\Http\Utility
	 */
	public function getHttpUtility() {
		if (empty($this->httpUtility)) {
			$this->httpUtility = new Http\Utility();
		}
		return $this->httpUtility;
	}

	/**
	 * Get the signature utility.
	 *
	 * @return OAuth\Server\Signature\Utility
	 */
	public function getSignatureUtility() {
		if (empty($this->signatureUtility)) {
			$this->signatureUtility = new Signature\Utility();
		}
		return $this->signatureUtility;
	}
	
	/**
	 * Set the storage adapter that the server will use to create and retrieve information.
	 *
	 * @throws InvalidArgumentException When the given adapter is not implementing OAuth\Server\Storage\StorageInterface
	 * @param StorageInterface $storage An OAuth\Server\Storage\StorageInterface instance.
	 * @return self
	 */
	public function setStorage(Storage\StorageInterface $storage) {
		$this->storage = $storage;
		return $this;
	}

	/**
	 * Get the current storage adapter.
	 *
	 * @throws RuntimeException Throw when force is TRUE and the storage adapter is not a implementing of OAuth\Server\Storage\StorageInterface
	 * @param bool $force Indicates if the storage adapter must be validated.
	 * @return OAuth\Server\Storage\StorageInterface|null An OAuth\Server\Storage\StorageInterface instance
	 */
	public function getStorage() {
		if (empty($this->storage)) {
			throw new \RuntimeException('No storage object has been set.');
		}
		if (!($this->storage instanceof \OAuth\Server\Storage\StorageInterface)) {
			throw new \RuntimeException('storage argument must be a instance of \OAuth\Server\Storage\StorageInterface');
		}
		
		return $this->storage;
	}

	public function setInitiateRequest($initiateRequest = null) {
		$this->initiateRequest = $initiateRequest;
		return $this;
	}

	/**
	 * @return OAuth\Server\Request\InitiateInterface
	 */
	public function getInitiateRequest() {
		if ($this->initiateRequest === null) {
			$this->initiateRequest = new Request\Initiate();
		} else {
			$this->initiateRequest = $this->getRequestInterface($this->initiateRequest, 'InitiateRequest');
		}

		return $this->initiateRequest;
	}

	public function setAuthorizeRequest($authorizeTokenRequest = null) {
		$this->authorizeRequest = $authorizeTokenRequest;
		return $this;
	}

	/**
	 * @return OAuth\Server\Request\AuthorizeInterface
	 */
	public function getAuthorizeRequest() {
		if ($this->authorizeRequest === null) {
			$this->authorizeRequest = new Request\Authorize();
		} else {
			$this->authorizeRequest = $this->getRequestInterface($this->authorizeRequest, 'AuthorizeRequest');
		}

		return $this->authorizeRequest;
	}

	public function setAccessTokenRequest($accessTokenRequest = null) {
		$this->accessTokenRequest = $accessTokenRequest;
		return $this;
	}

	/**
	 * @return OAuth\Server\Request\AccessTokenInterface
	 */
	public function getAccessTokenRequest() {
		if ($this->accessTokenRequest === null) {
			$this->accessTokenRequest = new Request\AccessToken();
		} else {
			$this->accessTokenRequest = $this->getRequestInterface($this->accessTokenRequest, 'AccessTokenRequest');
		}

		return $this->accessTokenRequest;
	}

	public function setAccessRequest($accessRequest = null) {
		$this->accessRequest = $accessRequest;
		return $this;
	}

	/**
	 * @return OAuth\Server\Request\AccessTokenInterface
	 */
	public function getAccessRequest() {
		if ($this->accessRequest === null) {
			$this->accessRequest = new Request\Access();
		} else {
			$this->accessRequest = $this->getRequestInterface($this->accessRequest, 'AccessRequest');
		}

		return $this->accessRequest;
	}

	protected function getRequestInterface($obj, $name) {
		if(is_string($obj) && class_exists($obj)) {
			$obj = new $obj();
		}

		if (!($obj instanceof \OAuth\Server\Request\RequestInterface)) {
			throw new \RuntimeException(sprintf('`%s` must be a instance of \OAuth\Server\Request\RequestInterface', $name));
		}
		return $obj;
	}
}
