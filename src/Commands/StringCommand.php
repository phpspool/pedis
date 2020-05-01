<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Commands;
use Spool\Pedis\Data\DbNode;
use Spool\Pedis\Data\KeyList;
use Spool\Pedis\Data\KeyNode;

/**
 * Description of StringCommand
 *
 * @author 陈浩波
 */
class StringCommand
{
    public function set(KeyList $keyList, string $key, string $value): array
    {
	$keyNode = $keyList->getKey($key);
	$keyNode->setType('string');
    }
}
