<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Commands;

use Spool\Pedis\Data\DbNode;
use Spool\Pedis\Data\KeyList;
/**
 * Description of ConnectionCommand
 *
 * @author 陈浩波
 */
class ConnectionCommand
{
    public function select(DbNode &$db, int $clientKey, int $dbIndex): array
    {
        return $db->select($clientKey, $dbIndex);
    }
    public function ping(DbNode &$db): array
    {
        return $db->ping();
    }
    public function echos(DbNode &$db, int $clientKey, string $msg): array
    {
        return $db->echos($msg);
    }
}
