<?php
class OauthController extends Zend_Controller_Action
{
	/**
	 * @var OAuth\Server\Server
	 */
	protected $server;

    public function init()
    {
		$storage = new OAuth\Server\Storage\Pdo\Storage(new PDO('mysql:host=localhost;dbname=oauth_test', '', ''));
		$config = new OAuth\Server\Config(array('storage' => $storage));
		$this->server = new OAuth\Server\Server($config);
    }

	/**
	 * Temporary Credential Request
	 * @return void
	 */
    public function initiateAction()
    {
        try {
            $this->server->initiate($this->getRequest());
        } catch(Exception $e) {
			$response = $this->getResponse()
				->clearAllHeaders()
				->setHttpResponseCode(401)
				->setHeader('Content-Type', 'text/plain');
			$response->setBody($e->getMessage());
			$response->sendResponse();
			exit();
        }
    }

	/**
	 * Resource Owner Authorization URI
	 * @return void
	 */
    public function authorizeAction()
    {
		//Zend_Auth::getInstance()->getIdentity()
		//var_dump('Create login form');

        $this->server->authorize($this->getRequest(), 1);
    }

	/**
	 * Token Request URI
	 * @return void
	 */
    public function tokenAction()
    {
        $this->server->accessToken($this->getRequest());
    }

	/**
	 * Token Request URI
	 * @return void
	 */
    public function hasaccessAction()
    {
        $this->server->access($this->getRequest());
    }
}

