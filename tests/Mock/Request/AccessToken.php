<?php
namespace Tests\Mock\Request;

class AccessToken extends \OAuth\Server\Request\AccessToken {
    public function validateArray(array $array)
	{
        return parent::validateArray($array);
	}
}
