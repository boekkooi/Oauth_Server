<?php
namespace OAuth\Server\Request;

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
class Authorize extends RequestAbstract
{
	/**
	 * Check what kind of request was made to the oauth server.
	 * @return void
	 */
	public function analyze(\Zend_Controller_Request_Http $httpRequest)
	{
		if (!($httpRequest instanceof \Zend_Controller_Request_Http)) {
			throw new \InvalidArgumentException('`request` must be a instance of `Zend_Controller_Request_Http`');
		}

        // Get the temporary credentials identifier
        $query = $httpRequest->getQuery();
        if (!empty($query['oauth_token'])) {
            $this->setRawParams($query);
			$this->requestScheme = \Zend_Oauth::REQUEST_SCHEME_QUERYSTRING;
            return;
        }

		// Sorry we can't handle this right now
		throw new \RuntimeException('Invalid or unknown type of request given.');
	}
}
