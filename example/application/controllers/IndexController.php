<?php

class IndexController extends Zend_Controller_Action
{
	/**
	 * @var Zend_Oauth_Consumer
	 */
	protected $consumer;

    public function init()
    {
		session_start();
		
		$host = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
		$options = array (
			'version' => '1.0', // there is no other version…
			'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
			'signatureMethod' => 'HMAC-SHA1',
			'callbackUrl' => $host . '/index/callback',
			'requestTokenUrl' => $host . '/oauth/initiate',
			'authorizeUrl' => $host . '/oauth/authorize',
			'accessTokenUrl' => $host . '/oauth/token',
			'consumerKey' => 'test',
			'consumerSecret' => 'oauthTest'
		);
		$this->consumer = new Zend_Oauth_Consumer($options);
    }

    public function indexAction()
    {
		$token = $this->consumer->getRequestToken();
		if ($token->getResponse() === null ||
			strcasecmp($token->getResponse()->getHeader('Content-Type'), 'application/x-www-form-urlencoded') !== 0) {
			$this->view->output = $token->getResponse()->getBody();
			return;
		}
		$_SESSION['client_tmp_token'] = serialize($token);
		$this->consumer->redirect();
    }

    public function callbackAction()
    {
        $token = unserialize($_SESSION['client_tmp_token']);
		if ($token) {
			$token = $this->consumer->getAccessToken($this->getRequest()->getQuery(), $token);
			if ($token->getResponse() === null ||
				strcasecmp($token->getResponse()->getHeader('Content-Type'), 'application/x-www-form-urlencoded') !== 0) {
				$this->view->output = $token->getResponse()->getBody();
				echo ($this->view->output);die;
				return;
			}
			unset($_SESSION['client_tmp_token']);
			$_SESSION['client_token'] = serialize($token);
			$this->_helper->redirector->gotoSimple('hasaccess');
		} else {
			var_dump('no session');
		}
		die;
	}

    public function hasaccessAction() {
        $token = unserialize($_SESSION['client_token']);
		$client = $token->getHttpClient(array(
			'consumerKey' => 'test',
			'consumerSecret' => 'oauthTest'
		));
		$client->setUri($host = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . '/oauth/hasaccess');
		$response = $client->request();
		echo $response->getBody();
		var_dump($_SESSION['client_token']);die;
	}


}

