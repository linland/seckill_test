<?php

/**
 * Created by ddt.
 * User: 谢林
 * Date: 2016/1/18 16:31
 * Description:
 */
class IndexController extends BasicController
{
    public function init(){
        $this->homeUrl = '/index/index';
    }
    public function indexAction(){
        return true;
    }
}