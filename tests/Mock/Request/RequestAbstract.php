<?php
namespace Tests\Mock\Request;

class RequestAbstract extends \OAuth\Server\Request\RequestAbstract {
    public function validateArray(array $array)
	{
        return parent::validateArray($array);
	}
}