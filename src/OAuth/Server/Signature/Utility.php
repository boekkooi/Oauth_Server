<?php
namespace OAuth\Server\Signature;

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
class Utility {
	/**
	 * Validate if the given signature method is a supported. 
	 *
	 * @param string $method The signature method.
	 * @return bool TRUE if the method is supported else FALSE.
	 */
    public function isValidSignatureMethod($method)
    {
		// Validate given method
		if (empty($method) || !in_array(strtoupper($method), array(
				'HMAC-SHA1', 'HMAC-SHA256', 'PLAINTEXT'
			))) {
            return false;
		}
        return true;
    }

	/**
	 * Get the class and hash algorithm belonging to the given signature method.
	 *
	 * @throws Zend_Oauth_Exception When a unsupported signature method is provided.
	 * @param string $method The signature method.
	 * @return array An array (0 => method class name, 1 => hash algorithm)
	 */
	protected function getSignatureInfo($method) {
		if (empty($method) || !$this->isValidSignatureMethod($method)) {
            throw new \RuntimeException('Unsupported signature method: '
                . $method
                . '. Supported are HMAC-SHA1, PLAINTEXT and HMAC-SHA256');
		}

		// Signature class
        $hashAlgorithm = null;
        $signatureMethod = strtoupper($method);
        $parts     = explode('-', $signatureMethod);
        if (count($parts) > 1) {
            $className = 'OAuth\Server\Signature\\' . ucfirst(strtolower($parts[0]));
            $hashAlgorithm  = $parts[1];
        } else {
            $className = 'OAuth\Server\Signature\\' . ucfirst(strtolower($signatureMethod));
        }

		return array($className, $hashAlgorithm);
	}

	/**
     * Verify a requests signature.
     *
	 * @param string $requestUrl
     * @param array $params
     * @param string $signatureMethod 
     * @param string $consumerSecret
     * @return boolean
     */
    public function verifySignature($requestUrl, array $params, $consumerSecret, $tokenSecret = null)
	{
		// Get the response method
		$responseMethod = \Zend_Oauth::POST;

		// Get the signature class and algorithm based on the given oauth_signature_method
		list($className, $hashAlgorithm) = $this->getSignatureInfo($params['oauth_signature_method']);

        // Create the signature class and verify the send oauth_signature
        $signatureObject = new $className($consumerSecret, $tokenSecret, $hashAlgorithm);
        $rtn = $signatureObject->verify($params['oauth_signature'], $params, $responseMethod, $requestUrl);

		// Some consumers don't agree with the optional part of oauth_version so let's add this extra check in case the signature fails
		if (!isset($params['oauth_version']) && $rtn === false) {
			$params['oauth_version'] = '1.0';
        	$rtn = $signatureObject->verify($params['oauth_signature'], $params, $responseMethod, $requestUrl);
		}

		return $rtn;
    }
}
