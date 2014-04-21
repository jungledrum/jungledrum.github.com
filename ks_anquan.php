<?php
/**
 * 安全联盟网站安全管家 v1.0
 * 2014.04.10
 */

define('KS_SAFE', 'POWERED BY KNOWNSEC');

ini_set('display_errors', 'off');
error_reporting(0);
set_time_limit(0);
ignore_user_abort(1);
ini_set('memory_limit', '128M');
date_default_timezone_set("GMT");

define('KS_SAFE_VERSION',               '1.0');
define('KS_SAFE_APP_ID',                'KS_ANQUAN_APP_ID');
define('KS_SAFE_SITE_ID',               '534fa5866e7fd67357aefc46');
define('KS_SAFE_TASK_ID',               'KS_ANQUAN_TASK_ID');
define('KS_SAFE_SECRET_ID',             '4bNqPgGufonFI0DyPEpkFQ6uPazYq8PWkDdjNf0MiDUd4kWJqb');
define('KS_SAFE_DOMAIN',                'http://zhanzhang.anquan.org/');  
define('KS_SAFE_PLATFORM',              KS_SAFE_DOMAIN.'anquan/');
define('KS_SAFE_LIB_PLATFORM',          KS_SAFE_DOMAIN.'guanjia/'); 
define('KS_SAFE_GET_APP_URL',           KS_SAFE_PLATFORM.'fetch-app/');
define('KS_SAFE_CHK_UPGRADE_URL',       KS_SAFE_PLATFORM.'check-upgrade/');
define('KS_SAFE_GET_UPGRADE_URL',       KS_SAFE_PLATFORM.'client-upgrade/');
define('KS_SAFE_GET_CONFIG_URL',        KS_SAFE_PLATFORM.'get-task-config/');
define('KS_SAFE_RET_BACKUP_HASH_URL',   KS_SAFE_PLATFORM.'receive-backup-hash/');
define('KS_SAFE_RET_BACKUP_URL',        KS_SAFE_PLATFORM.'receive-backup/');
define('KS_SAFE_RET_RESULT_URL',        KS_SAFE_PLATFORM.'task-result/');
define('KS_SAFE_RET_LOG_URL',           KS_SAFE_PLATFORM.'record-task-log/');
define('KS_SAFE_GET_LIB_URL',           KS_SAFE_LIB_PLATFORM.'filelist'); 

(@__DIR__ == '__DIR__') && define('__DIR__', realpath(dirname(__FILE__))); 
define('KS_SAFE_ROOT_DIR',              __DIR__);
define('KS_SAFE_KSAFE_DIR',             KS_SAFE_ROOT_DIR.DIRECTORY_SEPARATOR.'ksafe');
define('KS_SAFE_KSAFE_APP_DIR',         KS_SAFE_KSAFE_DIR.DIRECTORY_SEPARATOR.'app');
define('KS_SAFE_KSAFE_TMP_DIR',         KS_SAFE_KSAFE_DIR.DIRECTORY_SEPARATOR.'tmp');
define('KS_SAFE_KSAFE_LIB_DIR',         KS_SAFE_KSAFE_DIR.DIRECTORY_SEPARATOR.'lib');
define('KS_SAFE_KSAFE_BAK_DIR',         KS_SAFE_KSAFE_DIR.DIRECTORY_SEPARATOR.'bak');


if (!class_exists('KsFunc')) {
    class KsFunc {
        static function ks_include($strFileName) {
            return include($strFileName);
        }

        static function ks_include_once($strFileName) {
            return include_once($strFileName);
        }

        static function ks_is_file($strFileName) {
            return is_file($strFileName);
        }

        static function ks_is_dir($strDirName) {
            return is_dir($strDirName);
        }

        static function ks_file_exists($strFilePath) {
            return file_exists($strFilePath);
        }

        static function ks_unlink($strFilePath) {
            if (KsFunc::ks_is_file($strFilePath)) {
                return unlink($strFilePath);
            } else if (KsFunc::ks_is_dir($strFilePath)) {
                $arFiles = array_diff(scandir($strFilePath), array('.', '..'));
                foreach ($arFiles as $strEachFilePath) {
                    KsFunc::ks_unlink($strFilePath.DIRECTORY_SEPARATOR.$strEachFilePath);
                }
                return rmdir($strFilePath);
            } else {
                return 0;
            }
        }

        static function ks_touch($strFileName) {
            return touch($strFileName);
        }

        static function ks_chmod($strFilePath, $nMode) {
            return chmod($strFilePath, $nMode);
        }

        static function ks_mkdir($strDirName, $nMode=0777, $bRecursive=false) {
            return mkdir($strDirName, $nMode, $bRecursive);
        }

        static function ks_is_writable($strFilePath) {
            if(is_dir($strFilePath)) {
                if (substr($strFilePath, -1)!='/' or substr($strFilePath, -1)!='\\') {
                    $strFilePath = $strFilePath.DIRECTORY_SEPARATOR;
                }
                $strTmpFile = $strFilePath.md5(mt_rand().time());
                while (is_file($strTmpFile)) {
                    $strTmpFile = $strFilePath.md5(mt_rand().time());
                }
                if($fp = @fopen($strTmpFile, 'w')) {
                    @fclose($fp);
                    @unlink($strTmpFile);
                    $bWritable = 1;
                } else {
                    $bWritable = 0;
                }
            } else {
                if($fp = @fopen($strFilePath, 'a+')) {
                    @fclose($fp);
                    $bWritable = 1;
                } else {
                    $bWritable = 0;
                }
            }
            return $bWritable;
        }

        static function ks_is_readable($strFilePath) {
            return is_readable($strFilePath);
        }

        static function ks_file_get_contents($strFileName, $bUseIncludePath=false, $rContext=null) {
            // 请不要使用该函数做网络请求
            return file_get_contents($strFileName, $bUseIncludePath, $rContext);
        }

        static function ks_file_put_contents($strFileName, $strData, $nFlag=0, $rContext=null) {
            return file_put_contents($strFileName, $strData, $nFlag, $rContext);
        }

        # 以下这两个网络请求，不适合下载or上传大文件，V2可以包含自定义库
        static function ks_send_post_request($strUrl, $arData=array()) {
            $strData = http_build_query($arData);
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $strUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $strData);
                return curl_exec($ch);
            } else {
                $arOpts = array(
                    'http' => array(
                        'method' => "POST",
                        'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                                    "User-Agent: Mozilla/5.0\r\n",
                        'content' => $strData
                    )
                );
                $rContext = stream_context_create($arOpts);
                return  @file_get_contents($strUrl, 0, $rContext);
            }
        }

        static function ks_send_get_request($strUrl, $arData=array()) {
            $strData = http_build_query($arData);
            if ($strData) {
                $strUrl = $strUrl.'?'.$strData;
            }

            if (function_exists('curl_init')) {
                $ch = curl_init($strUrl) ;
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1) ;
                return curl_exec($ch) ;
            } else {
                $arOpts = array(
                    'http' => array(
                        'method' => "GET",
                        'header' => "User-Agent: Mozilla/5.0\r\n",
                    )
                );
                $rContext = stream_context_create($arOpts);
                return @file_get_contents($strUrl, 0, $rContext);
            }
        }

        static function ks_log($strContent) {
            KsFunc::ks_file_put_contents(KsVar::$strLogPath, $strContent."\r\n", FILE_APPEND);
        }

        static function ks_progress($nProgress) {
            KsFunc::ks_file_put_contents(KsVar::$strProgressPath, $nProgress);
        }

        static function ks_md5_file($strFileName) {
            $strFileContent = @KsFunc::ks_file_get_contents($strFileName);
            $strFileContent = str_replace(array("\n", "\r"), "", $strFileContent);
            return md5($strFileContent);
        }

        static function ks_md5_folder($dir) {
            if (!is_dir($dir)) {
                return false;
            }
            $filemd5s = array();
            $d = dir($dir);
            while (false !== ($entry = $d->read())) {
                if ($entry != '.' && $entry != '..' && $entry != '.svn') {
                    if (is_dir($dir.'/'.$entry)) {
                        $filemd5s[] = md5_folder($dir.'/'.$entry);
                    } else {
                        $filemd5s[] = @KsFunc::ks_md5_file($dir.'/'.$entry);
                    }
                }
            }
            $d->close();
            return md5(implode('', $filemd5s));
        }

        static function ks_get_app_abspath($nAppId) {
            return KsVar::$strKsafeAppDir.DIRECTORY_SEPARATOR.md5(KsVar::$strSiteId.$nAppId);
        }

        static function ks_create_tmp_file() {
            $strTmpFileName = KsVar::$strKsafeTmpDir.DIRECTORY_SEPARATOR.md5(mt_rand().time());
            while (KsFunc::ks_is_file($strTmpFileName)) {
                $strTmpFileName = KsVar::$strKsafeTmpDir.DIRECTORY_SEPARATOR.md5(mt_rand().time());
            }
            return $strTmpFileName;
        }

        static function ks_create_bak_file() {
            $strBakFileName = KsVar::$strKsafeBakDir.DIRECTORY_SEPARATOR.md5(mt_rand().time());
            while (KsFunc::ks_is_file($strBakFileName)) {
                $strBakFileName = KsVar::$strKsafeBakDir.DIRECTORY_SEPARATOR.md5(mt_rand().time());
            }
            return $strBakFileName;
        }
    }
}


if (!class_exists('KsVar')) {
    class KsVar {
        static public $strVersion           = KS_SAFE_VERSION;
        static public $strAppId             = KS_SAFE_APP_ID;
        static public $strSiteId            = KS_SAFE_SITE_ID;
        static public $strTaskId            = KS_SAFE_TASK_ID;
        static public $strSecretId          = KS_SAFE_SECRET_ID;

        static public $strDomain            = KS_SAFE_DOMAIN;

        static public $strGetAppUrl         = KS_SAFE_GET_APP_URL;
        static public $strChkUpdateUrl      = KS_SAFE_CHK_UPGRADE_URL;
        static public $strGetUpgradeUrl     = KS_SAFE_GET_UPGRADE_URL;
        static public $strGetConfigUrl      = KS_SAFE_GET_CONFIG_URL;
        static public $strRetBackupHashUrl  = KS_SAFE_RET_BACKUP_HASH_URL;
        static public $strRetBackupUrl      = KS_SAFE_RET_BACKUP_URL;
        static public $strRetResultUrl      = KS_SAFE_RET_RESULT_URL;
        static public $strRetLogUrl         = KS_SAFE_RET_LOG_URL;
        static public $strGetLibUrl         = KS_SAFE_GET_LIB_URL;

        static public $strRootDir           = KS_SAFE_ROOT_DIR;
        static public $strKsafeDir          = KS_SAFE_KSAFE_DIR;
        static public $strKsafeAppDir       = KS_SAFE_KSAFE_APP_DIR;
        static public $strKsafeTmpDir       = KS_SAFE_KSAFE_TMP_DIR;
        static public $strKsafeBakDir       = KS_SAFE_KSAFE_BAK_DIR;
        static public $strKsafeLibDir       = KS_SAFE_KSAFE_LIB_DIR;

        static public $strLogPath           = '';
        static public $strProgressPath      = '';
        static public $bRunning             = 0;

        static public $arAppResult          = array();
        static public $bAppStatus           = 0;
        static public $arAppConfig          = array();
        static public $arAppParam           = array();
        static public $strBackupMethod      = 'local';
        static public $arBackupFiles        = array();

        static public function chkSiteId() {
            if ( !(isset($_POST['site_id']) && $_POST['site_id'] === KsVar::$strSiteId) ) {
                die('KS_ANQUAN');
            }
        }

        static public function init() {
            KsVar::chkSiteId();

            KsVar::$strTaskId               = isset($_POST['task_id'])? $_POST['task_id']: '';
            KsVar::$strAppId                = isset($_POST['app_id'])? $_POST['app_id']: '';
            KsVar::$strLogPath              = KsVar::$strKsafeTmpDir.DIRECTORY_SEPARATOR.md5(KsVar::$strTaskId);
            KsVar::$strProgressPath         = KsVar::$strKsafeTmpDir.DIRECTORY_SEPARATOR.md5(md5(KsVar::$strTaskId));

            $arDirList  = array(
                KsVar::$strKsafeDir,
                KsVar::$strKsafeTmpDir,
                KsVar::$strKsafeLibDir,
                KsVar::$strKsafeBakDir,
                KsVar::$strKsafeAppDir
            );

            foreach($arDirList as $strDirName) {
                if (!KsFunc::ks_is_dir($strDirName)) {
                    @KsFunc::ks_mkdir($strDirName, 0755, 1);
                }
                if (KsFunc::ks_is_dir($strDirName)) {
                    @KsFunc::ks_chmod($strDirName, 0755);
                    @KsFunc::ks_touch($strDirName.DIRECTORY_SEPARATOR.'index.html');
                }
            }

            if (KsFunc::ks_is_file(KsVar::$strProgressPath)) {
                KsVar::$bRunning = 1;
            }

            @KsFunc::ks_touch(KsVar::$strLogPath);
            @KsFunc::ks_touch(KsVar::$strProgressPath);
        }
    }
}


if (!function_exists('catchFatalRuntimeError')) {
    /**
     * php 5.2.0以上可以使用register_shutdown_function 捕获致命异常
     * 在触发致命异常的时候，可以直接返回异常结果
     */
    function catchFatalRuntimeError() {
        if ($arError = error_get_last()) {
            if ($arError['type'] == E_ERROR) {
                $jAppResult = json_encode(array(
                    'info'      => '发生致命错误，请联系管理员',
                    'extinfo'   => array(
                                    array('文件', '行号', '错误信息'),
                                    array('file', 'line', 'message'),
                                    array($arError['file'], $arError['line'], $arError['message'])
                                   )
                ));

                $strResultHash  = md5('0'.KsVar::$strSecretId.
                    KsVar::$strSiteId.KsVar::$strTaskId.$jAppResult);

                $jSendData = json_encode(array(
                    'status'        => intval(KsVar::$bAppStatus),
                    'result'        => $jAppResult,
                    'site_id'       => KsVar::$strSiteId,
                    'task_id'       => KsVar::$strTaskId,
                    'result_hash'   => $strResultHash
                ));

                KsFunc::ks_send_post_request(KsVar::$strRetResultUrl, array('result'=> $jSendData));
            }
        }
    }
    @register_shutdown_function('catchFatalRuntimeError');
}


if (!class_exists('KsAnquan')) {
    class KsAnquan {

        function __construct() {
            KsVar::init();
        }

        function __destruct() {
            /**
             * 如果之前有同样的任务在跑，则不由这个任务删除文件
             */
            if (!KsVar::$bRunning) {
                if (KsFunc::ks_is_file(KsVar::$strLogPath)) {
                    @KsFunc::ks_unlink(KsVar::$strLogPath);
                }
                if (KsFunc::ks_is_file(KsVar::$strProgressPath)) {
                    @KsFunc::ks_unlink(KsVar::$strProgressPath);
                }
            }
        }

        public function upgrade() {
            // 触发升级操作
            $arResult = array(
                'status'=> 0 
            );

            if (!KsFunc::ks_is_writable(__FILE__)) {
                return json_encode($arResult);
            }

            $jSendData = json_encode(array(
                'site_id'       => KsVar::$strSiteId,
                'name'          => $_POST['name'],
                'upgrade_hash'  => md5(KsVar::$strSiteId.KsVar::$strSecretId)
            ));

            $arContent = json_decode(
                KsFunc::ks_send_post_request(
                    KsVar::$strGetUpgradeUrl, array("result"=> $jSendData)),
                1
            );

            $strUpdateHash = md5(md5($arContent['code'].KsVar::$strSecretId));
            if ($strUpdateHash === $arContent['upgrade_hash']) {
                @KsFunc::ks_file_put_contents(__FILE__, base64_decode($arContent['code']));
                return json_encode(array(
                    'status'    => 1,
                    'chksum'    => $arContent['md5sum'],
                    'version'   => $arContent['version']
                ));
            } else {
                return json_encode($arResult);
            }
        }

        public function initEnv() {
            $arResult = array(
                'status'=> -1
            );
            $arLibFile = json_decode(KsFunc::ks_send_get_request(KsVar::$strGetLibUrl), 1);

            foreach ($arLibFile as $strFileName=> $strDownLoadUrl) {
                $strAppFileName = KsFunc::ks_get_app_abspath($strFileName);
                $bStatus = @KsFunc::ks_file_put_contents(
                    $strAppFileName,
                    KsFunc::ks_send_get_request($strDownLoadUrl)
                );
                if (!$bStatus) {
                    return json_encode($arResult);
                }
            }

            return json_encode($arResult);
        }

        public function chkSelf() {
            $arResult = array(
                "status"=> -1  
            );

            if (!isset($_POST['md5'])) {
                return json_encode($arResult);
            }

            $strFileMd5 = KsFunc::ks_md5_file(__FILE__);

            if ($strFileMd5 === $_POST['md5']) {
                $arResult['status'] = 1;
                return json_encode($arResult);
            } else {
                return json_encode($arResult);
            }
        }

        public function installApp() {
            $arResult = array(
                "status"=> -1
            );

            $jSendData = json_encode(array(
                'app_id'    => KsVar::$strAppId,
                'site_id'   => KsVar::$strSiteId,
                'my_app'    => $_POST['my_app']
            ));

            $arContent = json_decode(KsFunc::ks_send_post_request(KsVar::$strGetAppUrl, array("result"=> $jSendData)),1);

            if (!$arContent) {
                return json_encode($arResult);
            }

            $strAppFileName = KsFunc::ks_get_app_abspath(KsVar::$strAppId);
            $bStatus = @KsFunc::ks_file_put_contents($strAppFileName, base64_decode($arContent['content']));

            if (!$bStatus) {
                return json_encode($arResult);
            }

            $arResult['status'] = 1;
            return json_encode($arResult);
        }

        public function chkApp() {
            $arResult = array(
                'status'=> 0
            );

            $strAppFileName = realpath(KsFunc::ks_get_app_abspath(KsVar::$strAppId));

            if (!$strAppFileName) {
                return json_encode($arResult);
            }

            if (isset($_POST['app_hash'])) {
                if (KsFunc::ks_md5_file($strAppFileName) != $_POST['app_hash']) {
                    return json_encode($arResult);
                }
            }

            $arResult['status'] = 1;
            return json_encode($arResult);
        }

        public function uninstallApp() {
            $arResult = array(
                'status'=> 0
            );

            $strAppFileName = realpath(KsFunc::ks_get_app_abspath(KsVar::$strAppId));
            if (!$strAppFileName) {
                return json_encode($arResult);
            }

            if (@unlink($strAppFileName)) {
                if ($_POST['name']=='filter') {
                    @unlink(KS_SAFE_KSAFE_APP_DIR."/ksafe_filter.php");
                }
                $arResult['status'] = 1;
                return json_encode($arResult);
            }else{
                return json_encode($arResult);
            }

        }

        public function chkProgress() {
            $arResult = array(
                'status'    => 0,
                'progress'  => 0
            );

            if (KsVar::$bRunning) {
                $strProgress = KsFunc::ks_file_get_contents(KsVar::$strProgressPath);
                $arResult['status'] = 1;
                $arResult['progress'] = intval($strProgress);
                return json_encode($arResult);
            } else {
                $arResult['status'] = 0;
                $arResult['progress'] = 100;
                return json_encode($arResult);
            }
        }

        public function chkEnv() {
            $arResult = array(
                'file_writable'     => '1',
                'dir_writable'      => '1',
                'network_status'    => '1'
            );

            if (!KsFunc::ks_is_writable(__FILE__)) {
                $arResult['file_writable'] = '0';
            }

            if (!(ini_get('allow_url_fopen') || function_exists('curl_init'))) {
                $arResult['network_status'] = '0';
            }

            if (!(KsFunc::ks_is_dir(KsVar::$strKsafeDir) && KsFunc::ks_is_writable(KsVar::$strKsafeDir)) ||
                !(KsFunc::ks_is_dir(KsVar::$strKsafeTmpDir) && KsFunc::ks_is_writable(KsVar::$strKsafeTmpDir)) ||
                !(KsFunc::ks_is_dir(KsVar::$strKsafeLibDir) && KsFunc::ks_is_writable(KsVar::$strKsafeLibDir)) ||
                !(KsFunc::ks_is_dir(KsVar::$strKsafeBakDir) && KsFunc::ks_is_writable(KsVar::$strKsafeBakDir)) ||
                !(KsFunc::ks_is_dir(KsVar::$strKsafeAppDir) && KsFunc::ks_is_writable(KsVar::$strKsafeAppDir))) {
                $arResult['dir_writable'] = '0';
            }

            return json_encode($arResult);
        }

        public function start() {
            $this->_chkEnv() && $this->_getConfig() && $this->_parseConfig() &&
            $this->_execApp() && $this->_uploadBackupHash() && $this->_uploadBackupFile();

            $this->_retResult();
            $this->_retLog();
            return 'start';
        }

        private function _chkEnv() {
            KsFunc::ks_log("[*] 开始检测环境");
            $arResult = array(
                'status'    => '0',
                'info'      => ''
            );

            $arData = json_decode($this->chkEnv(), 1);
            
            /*
            if (!$arData['file_writable']) {
                $arResult['info'] = "ks_anquan.php无法写入，请您修改其权限";
            } else 
            */
              
            if (!$arData['dir_writable']) {
                $arResult['info'] = "无法在根目录下创建ksafe目录，请您修改相应权限";
            } else if (!$arData['network_status']) {
                $arResult['info'] = "无法使用网络库，请您修改配置文件，将allow_url_fopen开启";
            } else {
                $arResult['status'] = '1';
            }

            KsFunc::ks_log("[*] 检测环境结束");
            if ($arResult['status'] == '1') {
                return 1;
            } else {
                KsVar::$bAppStatus  = 0;
                KsVar::$arAppResult = $arResult;
                return 0;
            }
        }

        private function _getConfig() {
            KsFunc::ks_log("[*] 正在下载配置文件");

            $jSendData = json_encode(array(
                'site_id'       => KsVar::$strSiteId,
                'task_id'       => KsVar::$strTaskId
            ));

            $arContent = json_decode(
                KsFunc::ks_send_post_request(
                    KsVar::$strGetConfigUrl, array("result"=> $jSendData)
                ),
                1
            );

            KsFunc::ks_log("[*] 下载配置文件结束");
            if ($arContent) {
                KsVar::$arAppConfig = $arContent;
                return 1;
            } else {
                KsVar::$bAppStatus  = 0;
                KsVar::$arAppResult = array('info'=> '下载配置文件出错，配置文件内容为空');
                return 0;
            }
        }

        private function _parseConfig() {
            KsFunc::ks_log("[*] 正在处理配置文件");
            $arConfig           = KsVar::$arAppConfig;
            KsVar::$bAppStatus     = 0;
            KsVar::$strAppId    = KsVar::$arAppConfig['app_id'];
            $strAppFileName     = realpath(KsFunc::ks_get_app_abspath(KsVar::$strAppId));

            if (!$strAppFileName) {
                KsFunc::ks_log("[-]  插件不存在，请下载该插件");
                KsVar::$arAppResult = array("info"=> "插件不存在，请下载该插件");
                return 0;
            }

            if (md5(md5($arConfig['param_code']).KsVar::$strSecretId) != $arConfig['param_hash']) {
                KsFunc::ks_log("[-] 更新的校验值不正确");
                KsFunc::ks_log("[-] 本地校验hash:  ".md5(md5($arConfig['param_code']).KsVar::$strSecretId));
                KsFunc::ks_log("[-] 远程校验hash:  ".$arConfig['param_hash']);
                KsVar::$arAppResult = array("info"=> "插件参数代码的校验代码不正确，请您重新下发任务");
                return 0;
            }

            KsVar::$arAppParam = json_decode(base64_decode($arConfig['param_code']), 1);
            KsFunc::ks_log("[*] 处理配置文件结束");
            return 1;
        }

        private function _execApp() {
            KsFunc::ks_log("[*] 正在执行插件");

            $strAppFileName = KsFunc::ks_get_app_abspath(KsVar::$strAppId);
            @KsFunc::ks_include_once($strAppFileName);

            $app = new App();
            $app->run();

            if (isset(KsVar::$arAppParam['backup_method']) &&
                KsVar::$arAppParam['backup_method'] == 'server') {
                KsVar::$strBackupMethod = 'server';
            } else {
                KsVar::$strBackupMethod = 'local';
            }

            KsFunc::ks_log("[*] 插件执行结束");
            return 1;
        }

        private function _uploadBackupHash() {
            KsFunc::ks_log("[*] 开始上传备份文件哈希");

            $strSiteId              = KsVar::$strSiteId;
            $arBackupFiles          = KsVar::$arBackupFiles;
            $strRetBackupHashUrl    = KsVar::$strRetBackupHashUrl;

            foreach ($arBackupFiles as $nFileId=> $arFileInfo) {
                
                if (is_dir($arFileInfo['backup_path'])) {
                    $strFileHash = KsFunc::ks_md5_folder($arFileInfo['backup_path']);
                }else{
                    $strFileHash = KsFunc::ks_md5_file($arFileInfo['backup_path']);
                }
                
                $jSendData = json_encode(array(
                    'site_id'       => $strSiteId,
                    'file_hash'     => $strFileHash
                ));

                $arContent = json_decode(
                    KsFunc::ks_send_post_request(
                      $strRetBackupHashUrl, array("result"=> $jSendData)
                    ),
                    1
                );

                if ($arContent) {
                    $arContent = array('status'=> 0);
                }

                $arFileInfo['file_hash']        = $strFileHash;
                $arFileInfo['backup_exists']    = $arContent['status'];
                KsVar::$arBackupFiles[$nFileId] = $arFileInfo;
            }

            KsFunc::ks_log("[*] 上传备份文件哈希结束");
            return 1;
        }

        private function _uploadBackupFile() {
            KsFunc::ks_log("[*] 开始上传备份文件");

            foreach (KsVar::$arBackupFiles as $arFileInfo) {
                if (!(KsFunc::ks_is_file($arFileInfo['backup_path']) ||
                    KsFunc::ks_is_dir($arFileInfo['backup_path']))) {
                    continue;
                }

                if (KsVar::$strBackupMethod == 'server' && $arFileInfo['backup_exists'] == '0' &&
                    !KsFunc::ks_is_dir($arFileInfo['backup_path'])) {
                    $strFileContent = base64_encode(@KsFunc::ks_file_get_contents($arFileInfo['backup_path']));
                } else {
                    $strFileContent = '';
                }

                $strOriginPath  = str_replace(KsVar::$strRootDir, '', $arFileInfo['origin_path']);
                $strBackupPath  = str_replace(KsVar::$strRootDir, '', $arFileInfo['backup_path']);
                $strFileHash    = $arFileInfo['file_hash'];
                $strBackupDesc  = isset($arFileInfo['backup_desc'])? $arFileInfo['backup_desc']: '';
                $strBackupHash  = md5(KsVar::$strSiteId.KsVar::$strSecretId.$strOriginPath.
                    $strBackupPath.$strFileContent.$strFileHash.KsVar::$strTaskId
                );

                $jSendData = json_encode(array(
                    'site_id'       => KsVar::$strSiteId,
                    'task_id'       => KsVar::$strTaskId,
                    'backup_hash'   => $strBackupHash,
                    'file_content'  => $strFileContent,
                    'file_hash'     => $strFileHash,
                    'backup_path'   => $strBackupPath,
                    'origin_path'   => $strOriginPath,
                    'backup_desc'   => $strBackupDesc
                ));

                KsFunc::ks_send_post_request(KsVar::$strRetBackupUrl, array('result'=> $jSendData));
            }

            KsFunc::ks_log("[*] 上传备份文件结束");
            return 1;
        }

        private function _retResult() {
            KsFunc::ks_log("[*] 开始上传APP执行结果");

            $jAppResult     = json_encode(KsVar::$arAppResult);
            $strResultHash  = md5(strval(intval(KsVar::$bAppStatus)).KsVar::$strSecretId.
                KsVar::$strSiteId.KsVar::$strTaskId.$jAppResult);

            $jSendData = json_encode(array(
                'status'        => intval(KsVar::$bAppStatus),
                'result'        => $jAppResult,
                'site_id'       => KsVar::$strSiteId,
                'task_id'       => KsVar::$strTaskId,
                'result_hash'   => $strResultHash
            ));

            KsFunc::ks_send_post_request(KsVar::$strRetResultUrl, array('result'=> $jSendData));
            KsFunc::ks_log("[*] 结束上传插件执行结果");
        }

        private function _retLog() {
            $strLogContent  = base64_encode(KsFunc::ks_file_get_contents(KsVar::$strLogPath));
            $strLogHash     = md5(KsVar::$strSiteId.KsVar::$strTaskId.$strLogContent.KsVar::$strSecretId);

            $jSendData = json_encode(array(
                'site_id'   => KsVar::$strSiteId,
                'task_id'   => KsVar::$strTaskId,
                'log'       => $strLogContent,
                'log_hash'  => $strLogHash,
            ));

            KsFunc::ks_send_post_request(KsVar::$strRetLogUrl, array('result'=> $jSendData));
        }
    }
}

$ks = new KsAnquan();

if (isset($_POST['act'])) {
    switch ($_POST['act']) {
        case 'upgrade':
            die($ks->upgrade());
            break;
        case 'init_env':
            die($ks->initEnv());
            break;
        case 'chk_md5':
            die($ks->chkSelf());
            break;
        case 'get_app':
            die($ks->installApp());
            break;
        case 'del_app':
            die($ks->uninstallApp());
            break;
        case 'chk_app':
            die($ks->chkApp());
            break;
        case 'chk_progress':
            die($ks->chkProgress());
            break;
        case 'chk_env':
            die($ks->chkEnv());
            break;
    }
} else if (isset($_POST['task_id'])){
    die($ks->start());
}
