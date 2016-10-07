<?php
namespace App\Shell\Home;
use App\Shell\MainController;
use App\Model\Access;
use App\Business\Backup\Calendar;
use App\Business\Backup\SystemInfo;
use App\Business\Backup\Manager;

/**
 *
 */
class Basic extends MainController
{

    /**
     *
     */
    public function backup()
    {
        // conf('db.mysql.user')
        show('<< Preview Mode >>');
        show('');

        show('Current');
        show('    ' . date('Y-m-d H:i:s'));
        show('');

        show('備份現況');
        $this->showBackups();

        // show('最後一次備份');
        // $this->showLastBackups();

        show('資料庫現況');
        $this->showDatabasesInfos();

        show('提示');
        show('    如果要執行, 請在 command line 後面加上 `yes` 參數');
        show('');
    }

    /**
     *  取得備份目錄的檔案
     *  比對之後顯示相關資訊
     */
    private function showBackups()
    {
        $tables = $this->getBackupTables();
        if (!$tables) {
            echo '    ';
            echo '沒有設定 config 內容';
            echo "\n\n";
            return;
        }

        foreach ($tables as $table) {

            //
            echo '    ';
            echo $table;
            echo "\n";

            $index = 1;
            $map = $this->getBackupMapByTableName($table);
            foreach ($map as $key => $value) {

                $key = (string) $key;
                $year  = substr($key, 0, 4);
                $month = substr($key, 4, 2);

                if (1===$index) {
                    echo '        ';
                    echo $year. ' :';
                }

                if ('yes' === $value) {
                    echo ' ' . $month;
                }
                elseif ('no' === $value) {
                    echo ' __';
                }
                elseif ('future' === $value) {
                    // 未來的事, 請忽略
                    echo '   ';
                }
                elseif ('focus' === $value) {
                    echo ' **';
                }
                else {
                    echo ' ??';
                }

                $index++;
                if ($index > 12) {
                    $index = 1;
                    echo "\n";
                }
            }

            if (!$map) {
                // 沒有之前的備份
                // 也沒有需要做備份的資料
                // 沒有辦法做備份
                echo "        Error ?";
                echo "\n\n";
                continue;
            }

            echo "\n";
        }

        echo <<<EOD
    [Tip]
        01 ~ 12 - Backuped
        __      - Not backup
        **      - 這次備份的目標, 如果沒有該月份的資料, 將不會產生備份檔
EOD;
        echo "\n\n";

    }

    /**
     *  將整個 備份表 在 視覺上 結構化
     *  備份的開始
     *      - 取決於最後一次備份的檔案 (檔案名稱中有標示 檔案日期)
     *  如果沒有任何備份
     *      - 以資料表中最新的那一個日期 做為開始
     *
     */
    private function getBackupMapByTableName($tableName)
    {
        $map = [];
        $currentDate = date('Ym');

        $myDates = SystemInfo::getBackupDatesByTableName($tableName);
        if ($myDates) {
            // 曾經備份過
            $firstYear   = (int) SystemInfo::getFirstYearByTableName($tableName);
            $currentYear = (int) date('Y');

            // 最後一次備份的日期 yyyy-mm
            $lastBackupDate = SystemInfo::getLastBackupByTableName($tableName);
        }
        else {
            // 從來沒有備份過
            // 就要以資料庫最早的時間, 做為備份的 開始 時間
            $tableInfo = $this->getDatabasesInfoByTableName($tableName);
            $firstDate = $tableInfo['results']['first'];
            if (!$firstDate) {
                // database error
                return [];
            }

            $firstYear   = (int) substr($firstDate, 0, 4);
            $currentYear = (int) date('Y');

            // 最後一次備份的日期 yyyy-mm
            // 必須使用資料表最早的那個日期
            $lastBackupDate = substr($firstDate, 0, 4) . substr($firstDate, 5, 2);
        }
        // dd_dump($myDates);
        // dd_dump($lastBackupDate);

        $map = Calendar::buildBetweenArray($firstYear, $currentYear);
        foreach ($map as $key => $value) {
            if ($key >= $currentDate) {
                $map[$key] = 'future';
            }
            elseif (in_array($key, $myDates)) {
                $map[$key] = 'yes';  // backuped
            }
            else {
                // 這裡的情況有兩種
                // 1. 以前未備份
                // 2. 目標備份
                if ($key >= $lastBackupDate) {
                    $map[$key] = 'focus';
                }
                else {
                    $map[$key] = 'no';
                }
            }
        }

        return $map;
    }

    /**
     *  資料表 的 資料 現況
     */
    private function getDatabasesInfoByTableName($tableName)
    {
        $tablesInfos = SystemInfo::getBackupTablesInfos();
        $table = null;
        $dateField = null;

        foreach ($tablesInfos as $tablesInfo) {
            if ($tablesInfo['table'] === $tableName) {
                 $table     = $tablesInfo['table'];
                 $dateField = $tablesInfo['date_field_name'];
                 break;
            }
        }
        if (!$table) {
            return [];
        }

        $result = [
            'table'             => $table,
            'date_field_name'   => $dateField,
            'error'             => '',
            'results' => [
                'first' => '',
                'last'  => '',
            ]
        ];

        $access = new Access($table, $dateField);

        $row = $access->getFristDate();
        $errorMessage = $access->getModelError();
        if ($errorMessage) {
            $result['error'] = 'Database Query Error: ' . $errorMessage;
            return $result;
        }

        $result['results']['first'] = $row['create_time'];

        $row = $access->getLastDate();
        $result['results']['last'] = $row['create_time'];

        return $result;
    }

    /**
     *  資料庫 資料 現況
     */
    private function showDatabasesInfos()
    {
        foreach ($this->getBackupTables() as $table) {

            $infos = $this->getDatabasesInfoByTableName($table);

            $table      = $infos['table'];
            $dateField  = $infos['date_field_name'];
            $error      = $infos['error'];

            echo '    ';
            echo $table;
            echo "\n";

            if ($error) {
                echo '        ';
                echo $error;
                echo "\n\n";
                continue;
            }

            $firstDate  = $infos['results']['first'];
            $lastDate   = $infos['results']['last'];

            echo '        ';
            echo 'first : ';
            echo $firstDate;
            echo "\n";

            echo '        ';
            echo 'last  : ';
            echo $lastDate;
            echo "\n";

            echo "\n";
        }
    }

    /**
     *  取得所有 "需要" 備份的 tables
     */
    private function getBackupTables()
    {
        return array_column( SystemInfo::getBackupTablesInfos(), 'table');
    }




    /*
    private function showLastBackups()
    {
        $tables = $this->getBackupTables();
        $len = 1;
        foreach ($tables as $table) {
            if (strlen($table) > $len) {
                $len = strlen($table);
            }
        }

        $format = "%-{$len}s";
        foreach ($tables as $table) {
            echo '    ';
            printf($format, $table);
            echo ' : ';
            echo SystemInfo::getLastBackupByTableName($table);
            echo ' (**個月以前) ';
            show('');
        }
        show('');
    }
    */

}
