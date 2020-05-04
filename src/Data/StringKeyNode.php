<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Data;
use Spool\Pedis\Lib\Log;
/**
 * Description of StringKeyNode
 *
 * @author 陈浩波
 */
class StringKeyNode extends KeyNode
{
    public function __construct()
    {
	$this->type = 'string';
        $this->data = 'nil';
    }
    public function set(array $value): array
    {
	$result = [
	    'code' => 1,
	    'msg' => 'ok'
	];
        Log::debug($value[0]);
	$this->data = $value[0];
	return $result;
    }
    public function get(): array
    {
	$result = [
	    'code' => 1,
	    'msg' => 'ok',
	    'data' => $this->data,
	];
	return $result;
    }
}
