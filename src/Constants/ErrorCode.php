<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Constants;

/**
 * Pedis报错信息,1000+启动错误|2000+DB错误|3000+KeyList错误|4000+KeyNode错误|9000+结束错误
 * 致命错误信息:< 0
 * @author 陈浩波
 */
class ErrorCode extends AbstractConstants
{
    
    /**
     * @Message('设置GID失败')
     */
    const UNABLE_TO_SETGID = -1001;
    /**
     * @Message('设置UID失败')
     */
    const UNABLE_TO_SETUID = -1002;
    /**
     * @Message('创建socket失败')
     */
    const FAILED_TO_CREATE_SOCKET = -1100;
    /**
     * @Message('绑定socket失败')
     */
    const FAILED_TO_BIND_TO_SOCKET = -1101;
    /**
     * @Message('监听socket失败')
     */
    const FAILED_TO_LISTEN_TO_SOCKET = -1102;
    /**
     * @Message('$config not is string or array!')
     */
    const CONFIG_NOT_STRING_OR_ARRAY = 1401;
    /**
     * @Message('pid文件已经存在')
     */
    const PID_FILE_IS_EXISTS = 1001;
    /**
     * @Message('fork子进程失败')
     */
    const FORK_FAILURE = 1101;
    /**
     * @Message('子进程独立失败')
     */
    const SET_SID_FAILUER = 1102;
    /**
     * @Message('不能选择超出配置的数据库')
     */
    const CANNOT_SELECT_LARGER_THAN_THE_CONFIGURATION_DATABASE = 2100;
    /**
     * @Message('数据格式错误')
     */
    const DATA_FORMATTING_ERROR = 4100;
    /**
     * @Message('pid文件没有移除')
     */
    const PID_FILE_NOT_REMOVE = 9100;
    
}
