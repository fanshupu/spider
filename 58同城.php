<?php 

// 获取58同城租房信息(带分页)(本地数据)(入库处理)(代码优化)

set_time_limit(0);
header('content-type:text/html; charset="utf-8"');

require './simple_html_dom/simple_html_dom.php';
$html = new simple_html_dom();

require './db.php';
require './get_result.php';


function get_data($page){

    global $html;

    // 页数
    for($i=1; $i<=$page; $i++){

        $url = "./html/58_{$i}.html"; // 使用的是本地数据
        echo '采集链接: ' . $url;
        echo '<br />=========================== 开始采集第' . $i . '页 ===================================<br />';
        $html->load_file($url);

        // 统计租房信息的个数
        $count = 0;
        foreach($html->find('.des') as $value){
            $count++;
        }
        echo '共' . $count . '条数据<br />';

        // 采集每条租房信息
        for($j=0; $j<$count/2; $j++){

            echo '这是第' . $j . '条<br />';

            // 标题和链接
            $title = filter($html->find('.des h2', $j)->find('a', 0)->innertext);
            $link  = filter($html->find('.des h2', $j)->find('a', 0)->href);
            echo '标题: ' . $title . ', 链接: ' . $link . '<br />';

            // 房子信息
            $info = filter($html->find('.des', $j)->find('.room', 0)->innertext);
            echo '房子信息: ' . $info . '<br />';

            // 地址
            echo '地址: ';
            foreach($html->find('.des .add', $j)->find('a') as $value){
                $address = filter($value->innertext);
                echo $value->innertext . '&nbsp;&nbsp;&nbsp;';
            }
            echo '<br />';

            // 月租
            $price = filter($html->find('.listliright', $j)->find('div[class="money"]', 0)->innertext);
            echo '租金: ' . $price . '<br />';

            // 入库处理
            $sql = "insert into spider_58(title, link, info, address, price) values('$title', '$link', '$info', '$address', '$price')";
            $result = add($sql);
            echo $result == true ? 'finish' : 'error';
            echo '<br /><hr />';
        }
        echo '第' . $i . '页采集完毕<br />';
        echo '======================================================================<br />';
        echo '======================================================================<br />';
    }
}

get_data(1);


/***
CREATE TABLE `spider_58` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(300) NOT NULL DEFAULT '' COMMENT '标题',
  `link` varchar(800) NOT NULL DEFAULT '' COMMENT '链接',
  `info` varchar(255) NOT NULL DEFAULT '' COMMENT '一些信息',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '位置',
  `price` varchar(30) NOT NULL DEFAULT '' COMMENT '价格',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

***/

 ?>