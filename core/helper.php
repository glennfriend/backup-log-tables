<?php

use Symfony\Component\VarDumper\Cloner\VarCloner;   // dd()
use Symfony\Component\VarDumper\Dumper\CliDumper;   // dd()

use App\Utility\Project\SlimManager;
use App\Utility\Config\Config;
use App\Utility\Console\CliManager;
use App\Utility\Console\ConsoleHelper;

// --------------------------------------------------------------------------------
//  Basic
// --------------------------------------------------------------------------------

/**
 *  是否為 開發環境
 */
function isTraining()
{
    if ('training' === conf('app.env')) {
        return true;
    }
    return false;
}

/**
 *  是否為 console line 環境
 */
function isCli()
{
    return PHP_SAPI === 'cli';
}

/**
 *  取得設定檔內容
 */
function conf($key)
{
    return Config::get($key);
}

/**
 *  取得專案路徑
 *      - 如果該程式移位, 請注意程式路徑可能要做變更
 *      - 不要自動修正 $url 前方有沒有 / 符號, 只要不符合規定就是錯誤
 *      - 不需要檢查路徑是否正確, 它不在該函式的範圍
 */
function getProjectPath($url='')
{
    if ($url && '/' !== mb_substr($url, 0, 1)) {
        throw new \Exception('Error: path not exact!');
    }

    return dirname(__DIR__) . $url;
}

// --------------------------------------------------------------------------------
//  Dependency Injection
// --------------------------------------------------------------------------------

/**
 *  包裝了 Symfony Dependency-Injection
 *  提供了簡易的取用方式 DI->get( $service )
 *
 *  @return Symfont Container ????
 */
function di($getParam=null)
{
    static $container;
    if ($container) {
        if ($getParam) {
            return $container->get($getParam);
        }
        return $container;
    }

    $container = new Symfony\Component\DependencyInjection\ContainerBuilder();
    return $container;
}

// --------------------------------------------------------------------------------
//
// --------------------------------------------------------------------------------

/**
 *  cc ccHelper function call
 *
 *  example:
 *      cc('date',   time()       );
 *      cc('escape', $articleText );
 *
 *  @param helper function name
 *  @param param2
 *  @param param3
 *  @param param4
 *  @param param5
 *  @return maybe have maybe not have
 */
function cc()
{
    $numArgs = func_num_args();
    $args    = func_get_args();
    $func    = $args[0];

    $functionFile = getProjectPath('/resource/ccHelper/' . $func . '.php');
    if (!file_exists($functionFile)) {
        throw new Exception('Error: cc helper "'. $func .'" function not fount!');
    }
    include_once ($functionFile);

    $name = 'ccHelper_'. $func;

    switch( $numArgs )
    {
        case 1: return $name();                                         exit;
        case 2: return $name( $args[1] );                               exit;
        case 3: return $name( $args[1], $args[2] );                     exit;
        case 4: return $name( $args[1], $args[2], $args[3]);            exit;
        case 5: return $name( $args[1], $args[2], $args[3], $args[4] ); exit;
        default:
            throw new Exception('Error: cc helper arguments to much');
    }
}

// --------------------------------------------------------------------------------
//  Output
// --------------------------------------------------------------------------------

/**
 *  linux console 版本的 pr()
 *  NOTE: 記得定時清理該內容
 *  使用方式
 *      -> tail -F var/out.log
 *
 */
function out($data)
{
    if (is_object($data) || is_array($data)) {
        $data = print_r($data, true);
    }
    else {
        $data .= "\n";
    }
    file_put_contents( getProjectPath('/var/out.log'), $data, FILE_APPEND);
}

/**
 *  show message, can write to log
 */
function show($data, $writeLog=false)
{
    if (is_object($data) || is_array($data)) {
        print_r($data);

        if ($writeLog) {
            di('log')->record(
                print_r($data, true)
            );
        }
    }
    else {
        echo $data;
        echo "\n";

        if ($writeLog) {
            di('log')->record($data);
        }
    }
}

/**
 * 將 2 維陣列直接輸出為 console table 的格式
 */
function table(Array $rows, $headers=null)
{
    if (isCli()) {
        if (null === $headers) {
            $headers = array_keys($rows[0]);
        }
        echo ConsoleHelper::table($headers, $rows);
    }
    else {
        if ($rows) {
            echo '<table style="border:1px solid; border-collapse:collapse; word-break:break-all; word-wrap:break-word; table-layout:fixed;">';
            echo '<tbody>';

            if ($headers) {
                echo '<tr>';
                foreach ($headers as $value) {
                    echo '<th>'. $value .'</th>';
                }
                echo '</tr>';
            }

            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>'. $value .'</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
    }
}

/**
 *  輸出
 */
function put($message=null)
{
    if(null === $message) {
        echo "\n";
        return;
    }

    switch (gettype($message)) {
        case "array":
        case "object":
        case "resource":
            print_r($message);
            break;

        case "integer":
        case "double":
        case "string":
            echo $message;
            echo "\n";
            break;

        case "NULL":
        case "boolean":
        case "unknown type":
            var_dump($message);
            break;

        default:
            die('put() Error: fasdfasdfasfadfasdfsad');
    };
}

/**
 *  get symfony var format
 *
 *  $type options
 *      null     => 顯示, 會自動判斷是不是 ILC
 *      'pre'    => 當作是 html 環境, 顯示 <pre></pre>
 *      'text'   => 當作是 CLI  環境, 顯示原始內容
 *
 *  @param any    $data
 *  @param string $type
 *  @return text
 */
function dd_dump($data, $type=null)
{
    $h = fopen('php://memory', 'r+b');
    $cloner = new VarCloner();
    $cloner->setMaxItems(-1);
    $dumper = new CliDumper($h, null);
    $dumper->setColors(false);

    $data = $cloner->cloneVar($data)->withRefHandles(false);
    $dumper->dump($data);
    $data = stream_get_contents($h, -1, 0);
    fclose($h);

    //
    // return or output
    //
    $type = trim(strtolower($type));

    if ('return' === $type) {
        return rtrim($data);
    }
    elseif ('pre' === $type) {
        echo '<pre>';
        print_r(rtrim($data));
        echo '</pre>';
        return;
    }
    elseif ('text' === $type) {
        print_r(rtrim($data));
        return;
    }

    // 自動判斷
    if (php_sapi_name() == "cli") {
        echo $data;
    }
    else {
        echo '<pre>';
        print_r(rtrim($data));
        echo '</pre>';
    }

}

/**
 *  symfony var dump + exit()
 *
 *  @param any $data
 */
function dd($data)
{
    dd_dump($data);
    exit;
}

