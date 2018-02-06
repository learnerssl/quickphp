<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/18
 * Time: 11:40
 * describe: 系统设置model
 */

namespace model\sys;

use model\Model;
use quickphp\lib\Cache;

class Basic extends Model
{
    const FILE = 'system_basic';

    /**
     * 编辑网站基本信息
     * @param $data
     * @return array|bool|int
     */
    public static function save($data)
    {
        //封装数据
        $data = array(
            'title' => $data['title'],
            'keyword' => $data['keyword'],
            'description' => $data['description'],
            'copyright' => $data['copyright'],
            'icp' => $data['icp'],
            'address' => $data['address'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'worktime' => $data['worktime'],
            'aboutus' => $data['aboutus']
        );

        //将系统配置信息生成静态缓存并返回结果
        $ret = Cache::getInstance(self::FILE)->setCache(json_encode($data, JSON_UNESCAPED_UNICODE));
        if ($ret <= 0) {
            return \common::output_error(ERR_SYSTEM_ERROR);
        }

        //返回
        return true;
    }

    /**
     * 获取系统设置信息
     * @return mixed
     */
    public static function given()
    {
        $base = Cache::getInstance(self::FILE)->getCache();
        return json_decode($base['data'], true);
    }
}