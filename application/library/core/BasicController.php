<?php

/**
 * Created by ddt.
 * User: 谢林
 * Date: 2016/1/18 15:57
 * Description: 基础控制器
 */
class BasicController extends Yaf_Controller_Abstract
{
    protected $homeUrl;

    public function get($key, $filter = TRUE)
    {
        if ($filter) {
            return filterStr($this->getRequest()->get($key));
        } else {
            return $this->getRequest()->get($key);
        }
    }

    public function getPost($key, $filter = TRUE)
    {
        if ($filter) {
            return filterStr($this->getRequest()->getPost($key));
        } else {
            return $this->getRequest()->getPost($key);
        }
    }

    public function getParam($key, $filter = TRUE)
    {
        if ($this->getRequest()->isGet()) {
            if ($filter) {
                return filterStr($this->getRequest()->get($key));
            } else {
                return $this->getRequest()->get($key);
            }
        } else {
            if ($filter) {
                return filterStr($this->getRequest()->getPost($key));
            } else {
                return $this->getRequest()->getPost($key);
            }
        }
    }

    public function getQuery($key, $filter = TRUE)
    {
        if ($filter) {
            return filterStr($this->getRequest()->getQuery($key));
        } else {
            return $this->getRequest()->getQuery($key);
        }
    }

    public function getSession($key)
    {
        return Yaf_Session::getInstance()->__get($key);
    }

    public function setSession($key, $val)
    {
        return Yaf_Session::getInstance()->__set($key, $val);
    }

    public function unsetSession($key)
    {
        return Yaf_Session::getInstance()->__unset($key);
    }

    // Clear cookie
    public function clearCookie($key)
    {
        setCookie($key, '');
    }

    /**
     * 验证是否ajax提交
     * @return bool
     */
    public function isAjax(){
        return $this->getRequest()->isXmlHttpRequest();
    }
    /**
     * 验证是否post提交
     * @return bool
     */
    public function isPost(){
        return $this->getRequest()->isPost();
    }

    /**
     * 验证是否get提交
     * @return bool
     */
    public function isGet(){
        return $this->getRequest()->isGet();
    }

    /**
     * Set COOKIE
     */
    public function setCookie($key, $value, $expire = 3600, $path = '/', $domain = '', $httpOnly = FALSE)
    {
        setCookie($key, $value, CUR_TIMESTAMP + $expire, $path, $domain, $httpOnly);
    }

    /**
     * 获取cookie
     */
    public function getCookie($key)
    {
        return trim($_COOKIE[$key]);
    }

    // Load model
    public function load($model){
        return Helper::load($model);
    }

    // Go home
    public function goHome(){
        jsRedirect($this->homeUrl);
    }
}