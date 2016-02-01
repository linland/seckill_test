<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15-9-30
 * Time: 上午11:51
 */

class TestController extends BasicController{
    public function indexAction(){
        echo '2';
        return false;
    }
}