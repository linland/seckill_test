<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    private $arrConfig;
    public function _initConfig() {
		//把配置保存起来
		$this->arrConfig = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $this->arrConfig);
	}

	// Load libaray, MySQL model, function
	public function _initCore() {
		// 设置自动加载的目录
		ini_set('yaf.library', LIB_PATH);

		// 加载核心组件
		Yaf_Loader::import(CORE_PATH.'/BasicController.php');
		Yaf_Loader::import(CORE_PATH.'/Helper.php');
		Yaf_Loader::import(CORE_PATH.'/Model.php');
        Yaf_Loader::import(CORE_PATH.'/Rdb.php');

		// 导入 FunctionBasic.php
		Yaf_Loader::import(FUNC_PATH.'/FunctionBasic.php');
	}

    public function _initRedis() {
        $cache_config['port'] = $this->arrConfig->redis_port;
        $cache_config['host'] = $this->arrConfig->redis_host;

        Yaf_Registry::set('redis', new Rdb($cache_config));
    }

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		//注册一个插件
		$objSamplePlugin = new SamplePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}
	
	public function _initView(Yaf_Dispatcher $dispatcher){
		//在这里注册自己的view控制器，例如smarty,firekylin
	}
}
