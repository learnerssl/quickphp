<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/25
 * Time: 23:16
 */

namespace quickphp\lib;
class Page
{
    private static $_instance;

    private function __construct()
    {
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
     * 获取分页
     * @param int $idx 当前页码
     * @param int $count
     * @param int $size
     * @return mixed
     */
    public function get_page($idx, $count, $size)
    {
        //处理参数
        $_SERVER['REQUEST_URI'] = preg_replace('/?page=\d*/', '', $_SERVER['REQUEST_URI']);

        //获取总页数
        $total_page = ceil($count / $size);

        //上一页
        $i_prev = $idx - 1 <= 0 ? 1 : $idx;

        //下一页
        $i_next = $idx + 1 >= $total_page ? $total_page : $idx;

        $page = null;
        $page .= '<nav aria-label="Page navigation">
    				<ul class="pagination">';
        $page .= '    <li>
		               <a href="' . $_SERVER['REQUEST_URI'] . "?page=" . $i_prev . '" aria-label="Previous">
		                <span aria-hidden="true">&laquo;</span>
		               </a>
		              </li>';
        for ($i = 1; $i <= $total_page; $i++) {
            if ($i == $idx) {
                $active = ' class="active"';
            }
            $page .= '<li' . $active . '>
						<a href="' . $_SERVER['REQUEST_URI'] . "?page=" . $i . '">' . $i . '</a>
					  </li>';
            unset($active);
        }
        $page .= '    <li>
		                <a href="' . $_SERVER['REQUEST_URI'] . "?page=" . $i_next . '" aria-label="Next">
		                  <span aria-hidden="true">&raquo;</span>
		                </a>
		              </li>';
        $page .= "    <li>
					    <a>共{$count}条,共计{$total_page}页</a>
				      </li>";
        $page .= '  </ul>
				  </nav>';

        return $page;
    }
}