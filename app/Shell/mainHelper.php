<?php
use App\Utility\Console\CliManager as CliManager;

/**
 *  取得 command line 處理之後獲得的參數
 */
function getParam($key, $defaultValue=null)
{
    return CliManager::get($key);
}
