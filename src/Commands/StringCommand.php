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
use Spool\Pedis\Data\StringKeyNode;
use Spool\Pedis\Lib\Log;

/**
 * Description of StringCommand
 *
 * @author 陈浩波
 */
class StringCommand
{

    public function set(DbNode &$dbNode, int $clientKey, string $key, array $value): array
    {
        $keyNode = $dbNode->getClientDb($clientKey)->getStringKey($key);
        Log::debug($value);
        return $keyNode->set($value);
    }

    public function get(DbNode &$dbNode, int $clientKey, string $key): array
    {
        if ($dbNode->getClientDb($clientKey)->exists($key)) {
            return $dbNode->getClientDb($clientKey)->getStringKey($key)->get();
        } else {
            return [
                'code' => 1,
                'msg'  => 'ok',
                'data' => 'nil',
            ];
        }
    }

}
