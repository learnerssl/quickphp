<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."sphinx/sphinxapi.php");

/**
 * @package SlightPHP
 */
class SSphinx extends SphinxClient{
	private $_page=1;
	private static $_config;

	/**
	 * 设置sphinx的配置文件
	 * @param $file
	 */
	static function setConfigFile($file){
		self::$_config = $file;
	}

	public function __construct()
	{
		$this->SphinxClient();
		//获取配置参数
		$cfg = SConfig::getConfig(ROOT_CONFIG."/cache.ini",'sphinx');
		$this->_host = $cfg->host;
		$port = intval($cfg->port);
		assert ( 0<=$port && $port<65536 );
		$this->_port = ( $port==0 ) ? 9312 : $port;
		$this->_timeout = intval($cfg->timeout);
	}

	function setPage($page)
	{
		$this->_page = $page;
		$this->SetLimits (($this->_page-1)*$this->_limit,$this->_limit);
	}
	function setLimit($limit)
	{
		$this->_limit = $limit;
		$this->SetLimits (($this->_page-1)*$this->_limit,$this->_limit);
	}

	/**
	 * 重构父方法，让其失效
	 * @param $host
	 * @param int $port
	 */
	function SetServer ( $host, $port = 0 )
	{}

}
?>