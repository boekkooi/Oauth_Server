<?php
namespace Tests\Mock\Request;

class Access extends \OAuth\Server\Request\Access {
    public function validateArray(array $array)
	{
        return parent::validateArray($array);
	}
}
