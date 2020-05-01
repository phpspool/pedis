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
    public function select(DbNode &$db, int $dbIndex, int $clientKey): array
    {
        return $db->select($dbIndex, $clientKey);
    }
    
}
