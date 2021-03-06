<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/28
 * Time: 22:27
 * describe: Fill in the description of the document here
 */

namespace application\web\admin\v1;

use application\Controller;
use model\crm\User;
use quickphp\lib\Redis;

class AdminController extends Controller
{
    protected $uid;
    protected $param;

    /**
     * AdminController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        //获取用户id
        $this->uid = end(explode(':', Redis::getInstance()->get('user')));
        if ($this->uid) {
            $this->param['info'] = User::getInstance()->get_one_date_by_pkey($this->uid);
        }
    }
}