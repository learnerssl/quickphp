<?php
require_once ROOT.'/extend/sphinx/sphinxapi.php';

/**
 * require QUICKPHP.'/lib/SSphinx.class.php';
    $cl = new \SSphinx();
    $cl->SetServer("localhost", 9312);
    $cl->SetMatchMode(SPH_MATCH_EXTENDED);
    $cl->SetArrayResult(true);
    $result = $cl->Query('','test1');
 * Class SSphinx
 */
class SSphinx extends SphinxClientServer
{
    private $_page = 1;
    private static $_config;

    public function __construct()
    {
        $this->SphinxClient();

        //获取配置参数
        self::$_config = \config::$sphinx_conf;
        $this->_host = self::$_config['host'];
        $port = intval(self::$_config['port']);
        assert(0 <= $port && $port < 65536);
        $this->_port = ($port == 0) ? 9312 : $port;
        $this->_timeout = intval(self::$_config['timeout']);
    }

    function setPage($page)
    {
        $this->_page = $page;
        $this->SetLimits(($this->_page - 1) * $this->_limit, $this->_limit);
    }

    function setLimit($limit)
    {
        $this->_limit = $limit;
        $this->SetLimits(($this->_page - 1) * $this->_limit, $this->_limit);
    }

    /**
     * 重构父方法，让其失效
     * @param $host
     * @param int $port
     */
    function SetServer($host, $port = 0)
    {

    }
}
