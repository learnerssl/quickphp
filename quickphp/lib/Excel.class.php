<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/13
 * Time: 13:27
 */

/**
 * phpexcel类
 */
class Excel
{
    private $objPHPExcel;
    private static $_instance;

    private function __construct()
    {
        //导入相关库
        include_once ROOT . "/extend/phpexcel/Classes/PHPExcel.php";
        $this->objPHPExcel = new PHPExcel();
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 导出excel文档
     * @param $meta $meta excel头部信息 [{name:列导出数据的键,title:列标题,width:列长度}]
     * @param $list $list 导出数据
     * @param string $title $title excel文档名字
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public static function export_excel($meta, $list, $title = '导出Excel文档')
    {
        //实例化相关类
        $objPHPExcel = new PHPExcel();

        //设置Excel文件信息
        $objPHPExcel->getProperties()->setTitle($title);

        //设置excel场景
        $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet = $objPHPExcel->getActiveSheet();

        //设置头部信息，data写入excel场景1
        $idx = 0;
        $cnt = count($list);
        do {
            $item = $list[$idx];
            foreach ($meta as $key1 => $item1) {
                if ($idx == 0) {
                    $activeSheet->getColumnDimensionByColumn($key1)->setWidth($item1['width']);
                    $activeSheet->setCellValueByColumnAndRow($key1, $idx + 1, $item1['title']);
                }
                $activeSheet->setCellValueByColumnAndRow($key1, $idx + 2, \common::get_array_value_by_key($item, $item1['name'], ''));
            }
            $idx++;
        } while ($idx < $cnt);

        //输出xls文件
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 用户数据表导出(实例)
     * @param $list $list 引用数据表
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    function excel(&$list)
    {
        //标题组装
        $meta[] = array('name' => 'a', 'title' => 'UID', 'width' => 10);
        $meta[] = array('name' => 'b', 'title' => '用户姓名', 'width' => 20);
        $meta[] = array('name' => 'c', 'title' => '联系电话', 'width' => 20);
        $meta[] = array('name' => 'd', 'title' => '城市', 'width' => 20);
        $meta[] = array('name' => 'e', 'title' => '当前身份', 'width' => 20);
        $meta[] = array('name' => 'f', 'title' => '账户余额', 'width' => 20);
        $meta[] = array('name' => 'g', 'title' => '注册日期', 'width' => 20);
        $meta[] = array('name' => 'h', 'title' => '最后登录', 'width' => 20);
        $meta[] = array('name' => 'i', 'title' => '来源', 'width' => 20);
        $meta[] = array('name' => 'j', 'title' => '下级', 'width' => 20);
        $meta[] = array('name' => 'k', 'title' => '账号状态', 'width' => 10);

        //数据整理
        foreach ($list as $key => $item) {
            //数据组装
            $item_t = [
                'a' => $item['uid'],
                'b' => $item['uname'],
                'c' => $item['mobile'],
                'd' => $item['rtext'],
                'e' => $item['ttext'],
                'f' => isset($item['value.tmny']) ? $item['value.tmny'] : '',
                'g' => $item['atime'],
                'h' => $item['ltime'],
                'i' => $item['ftext'],
                'j' => $item['nextcount'],
                'k' => $item['sstext']
            ];

            //替换
            $list[$key] = $item_t;
        }

        //导出excel5
        $title = '导出Excel_' . date('YmdHis', time());
        $this->export_excel($meta, $list, $title);
        exit;
    }
}

