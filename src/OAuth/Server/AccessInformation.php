<?php
namespace OAuth\Server;

/**
 * @author Warnar Boekkooi
 * Created on: 22-11-10 16:52
 */ 
class AccessInformation {
	protected $userId;

	protected $consumerId;

	public function setConsumerId($consumerId)
	{
		$this->consumerId = $consumerId;
	}

	public function getConsumerId()
	{
		return $this->consumerId;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}
}
