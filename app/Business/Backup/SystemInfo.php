<?php
namespace App\Business\Backup;

/**
 *  Backup SystemInfo
 */
class SystemInfo
{
    /**
     *  取得所有 "需要" 備份的 tables infos
     */
    static public function getBackupTablesInfos()
    {
        return conf('settings');
    }

    /**
     *  取得 "已備份" 的 日期 資訊 by table name
     */
    static public function getBackupPath()
    {
        return getProjectPath(conf('app.backup_path'));
    }

    /**
     *  取得 "已備份" 的 日期 資訊 by table name
     */
    static public function getBackupDatesByTableName($tableName)
    {
        $filenames = [];
        $backupPath = self::getBackupPath();

        $display = '';
        $files = glob("{$backupPath}/{$tableName}.*.sql");
        foreach ($files as $pathFile) {
            $filename = basename($pathFile);
            $filenames[] = substr($filename, -10, 6);
        }

        return $filenames;
    }

    /**
     *  在已經備份的檔案中
     *  找到 最早備份 的年份
     *
     *  @return string "yyyy"
     */
    static public function getFirstYearByTableName($tableName)
    {
        $myDates = SystemInfo::getBackupDatesByTableName($tableName);
        if (!$myDates) {
            return '';
        }

        return substr($myDates[0], 0, 4);
    }

    /**
     *  在已經備份的檔案中
     *  找到 最後備份 的 年份 + 月份
     *
     *  @return string "yyyymm"
     */
    static public function getLastBackupByTableName($tableName)
    {
        $myDates = SystemInfo::getBackupDatesByTableName($tableName);
        if (!$myDates) {
            return '';
        }

        $lastIndex = count($myDates) - 1;
        return $myDates[$lastIndex];
    }

}
