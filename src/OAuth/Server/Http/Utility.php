<?php
namespace OAuth\Server\Http;

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
class Utility extends \Zend_Oauth_Http_Utility {
    /**
     * Parse `Authorization` header string
     *
     * @param  mixed $query
     * @return array
     */
    public function parseAuthorizationHeader($query)
    {
        $params = array();
        if (empty($query)) {
            return $params;
        }

        // Not remotely perfect but beats parse_str() which converts
        // periods and uses urldecode, not rawurldecode.
        $parts = explode(',', $query);
        foreach ($parts as $pair) {
            $kv = explode('=', $pair);
			$v = trim(rawurldecode($kv[1]), '"');
            $params[rawurldecode($kv[0])] = $v;
        }
        return $params;
    }
}
