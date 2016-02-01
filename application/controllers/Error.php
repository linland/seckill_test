<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends Yaf_Controller_Abstract {

	//从2.1开始, errorAction支持直接通过参数获取异常
	public function errorAction($exception) {
        switch ($exception->getCode()) {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
                if(ENV == 'DEV'){
                    //echo 404, ":", $exception->getMessage();
                }else{
                    echo 404;
                    file_put_contents(LOG_FILE, $exception->getMessage().PHP_EOL, FILE_APPEND);
                }
                break;

            default :
                if(ENV == 'DEV'){
                    //echo 0, ":", $exception->getMessage();
                }else{
                    //echo 'Unknown error';
                    file_put_contents(LOG_FILE, $exception->getMessage().PHP_EOL, FILE_APPEND);
                }
                break;
        }
        $this->getView()->assign("exception",$exception);
	}
}
