<?php
/**
 * DouPHP
 * --------------------------------------------------------------------------------------------------
 * 版权所有 2013-2018 漳州豆壳网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.douco.com
 * --------------------------------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在遵守授权协议前提下对程序代码进行修改和使用；不允许对程序代码以任何形式任何目的的再发布。
 * 授权协议：http://www.douco.com/license.html
 * --------------------------------------------------------------------------------------------------
 * Author: DouCo
 * Release Date: 2018-05-23
 */
define('IN_DOUCO', true);

require (dirname(__FILE__) . '/include/init.php');

// rec操作项的初始化
$rec = $check->is_rec($_REQUEST['rec']) ? $_REQUEST['rec'] : 'default';

// 图片上传
include_once (ROOT_PATH . 'include/upload.class.php');
$images_dir = 'images/article/'; // 文件上传路径，结尾加斜杠
$img = new Upload(ROOT_PATH . $images_dir); // 实例化类文件
if (!file_exists(ROOT_PATH . $images_dir))
    mkdir(ROOT_PATH . $images_dir, 0777);

// 赋值给模板
$smarty->assign('rec', $rec);
$smarty->assign('cur', 'zuzhi');

/**
 * +----------------------------------------------------------
 * 文章列表
 * +----------------------------------------------------------
 */
if ($rec == 'default') {
    $smarty->assign('ur_here', $_LANG['zuzhi']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['zuzhi_add'],
            'href' => 'zuzhi.php?rec=add' 
    ));
    
    // 获取参数
    $zid = $check->is_number($_REQUEST['zid']) ? $_REQUEST['zid'] : 0;
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    $where='';
    // 筛选条件
    /*
    $where = ' WHERE cat_id IN (' . $o_id . $dou->dou_child_id('article_category', $o_id) . ')';
      */
    if ($keyword) {
        $where = " where zname LIKE '%$keyword%' ";
        $get = '&keyword=' . $keyword;
    }
  
 
    // 分页
    $page = $check->is_number($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $page_url = 'zuzhi.php' . ($zid ? '?zid=' . $zid : '');
    $limit = $dou->pager('zuzhi', 15, $page, $page_url, $where, $get);
    
    $sql = "SELECT * FROM " . $dou->table('zuzhi') . $where . " ORDER BY cid asc" . $limit;
//echo $sql;
    $query = $dou->query($sql);
    while ($row = $dou->fetch_array($query)) {
       // $cat_name = $dou->get_one("SELECT cat_name FROM " . $dou->table('article_category') . " WHERE cat_id = '$row[cat_id]'");
      //  $add_time = date("Y-m-d", $row['add_time']);
        
        $zuzhi_list[] = array (
                "zid" => $row['zid'],
             //   "cat_id" => $row['cat_id'],
           //     "cat_name" => $cat_name,
                "zname" => $row['zname'],
        		"parent_id"  =>$row['parent_id'],
        		"cid" =>$row['cid']
              //  "add_time" => $add_time 
        );
    }
 
    // 赋值给模板
   // $smarty->assign('sort', $dou->get_sort('zuzhi', 'zid'));
   // $smarty->assign('cat_id', $cat_id);
    $smarty->assign('keyword', $keyword);
//    $smarty->assign('zuzhi_category', $dou->get_category_nolevel('article_category'));
    $smarty->assign('zuzhi_list', $zuzhi_list);
    
    
    //查询所有的组织人员
   $tree_zuzhi= get_tree_zuzhi();
 // print_r($tree_zuzhi);
   $smarty->assign('tree_zuzhi', $tree_zuzhi);
    
    $smarty->display('zuzhi.htm');
} 

/**
 * +----------------------------------------------------------
 * 文章添加
 * +----------------------------------------------------------
 */
elseif ($rec == 'add') {
    $smarty->assign('ur_here', $_LANG['zuzhi_add']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['zuzhi'],
            'href' => 'article.php' 
    ));
    
    // 格式化自定义参数，并存到数组$article，文章编辑页面中调用文章详情也是用数组$article，
    if ($_DEFINED['article']) {
        $defined = explode(',', $_DEFINED['article']);
        foreach ($defined as $row) {
            $defined_article .= $row . "：\n";
        }
        $article['defined'] = trim($defined_article);
        $article['defined_count'] = count(explode("\n", $article['defined'])) * 2;
    }
    
    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
    
    // 赋值给模板
    $smarty->assign('form_action', 'insert');
    $smarty->assign('article_category', $dou->get_category_nolevel('article_category'));
    $smarty->assign('article', $article);
    
    $smarty->display('zuzhi.htm');
} 

elseif ($rec == 'insert') {
    // 验证标题
    if (empty($_POST['title'])) $dou->dou_msg($_LANG['article_name'] . $_LANG['is_empty']);
    
    // 图片上传
    if ($_FILES['image']['name'] != "")
        $image = $images_dir . $img->upload_image('image', $img->create_file_name('article'));
    
    // 数据格式化
    $add_time = time();
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
        
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
    
    $sql = "INSERT INTO " . $dou->table('article') . " (id, cat_id, title, defined, content, image, keywords, description, add_time)" . " VALUES (NULL, '$_POST[cat_id]', '$_POST[title]', '$_POST[defined]', '$_POST[content]', '$image', '$_POST[keywords]', '$_POST[description]', '$add_time')";
    $dou->query($sql);
    
    $dou->create_admin_log($_LANG['article_add'] . ': ' . $_POST['title']);
    $dou->dou_msg($_LANG['article_add_succes'], 'article.php');
} 

/**
 * +----------------------------------------------------------
 * 文章编辑
 * +----------------------------------------------------------
 */
elseif ($rec == 'edit') {
    $smarty->assign('ur_here', $_LANG['zuzhi_edit']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['zuzhi'],
            'href' => 'zuzhi.php' 
    ));
    
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : '';
    
    $query = $dou->select($dou->table('zuzhi'), '*', '`zid` = \'' . $id . '\'');
    $zuzhi = $dou->fetch_array($query);
    //查询当前所有的上级
   if($zuzhi['cid']>1){
	$parent_cid=$zuzhi['cid']-1;
    $zuzhi['parent']=$dou->getAll("select * from m_zuzhi where cid=$parent_cid");
   }
   // print_r($zuzhi);
    // 格式化自定义参数
    if ($_DEFINED['zuzhi'] || $zuzhi['defined']) {
        $defined = explode(',', $_DEFINED['zuzhi']);
        foreach ($defined as $row) {
            $defined_article .= $row . "：\n";
        }
        // 如果文章中已经写入自定义参数则调用已有的
        $zuzhi['defined'] = $zuzhi['defined'] ? str_replace(",", "\n", $zuzhi['defined']) : trim($defined_article);
        $zuzhi['defined_count'] = count(explode("\n", $zuzhi['defined'])) * 2;
    }
    
    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
    
    // 赋值给模板
    $smarty->assign('form_action', 'update');
   // $smarty->assign('article_category', $dou->get_category_nolevel('article_category'));
    $smarty->assign('zuzhi', $zuzhi);
    
    $smarty->display('zuzhi.htm');
} 

elseif ($rec == 'update') {
    // 验证标题
    if (empty($_POST['name'])) $dou->dou_msg($_LANG['zuzhi_name'] . $_LANG['is_empty']);
        
    // 图片上传
   // if ($_FILES['image']['name'] != "")
    //    $image = ", image = '" . $images_dir . $img->upload_image('image', $img->create_file_name('article', $_POST['id'], 'image')) . "'";
    
    // 格式化自定义参数
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
    
    $sql = "UPDATE " . $dou->table('zuzhi') . " SET cid = '$_POST[cid]', zname = '$_POST[name]', code = '$_POST[code]', parent_id = '$_POST[parent_id]'  WHERE zid = '$_POST[id]'";
    $dou->query($sql);
    
    $dou->create_admin_log($_LANG['zuzhi_edit'] . ': ' . $_POST['name']);
    $dou->dou_msg($_LANG['zuzhi_edit_succes'], 'zuzhi.php');
} 

/**
 * +----------------------------------------------------------
 * 文章删除
 * +----------------------------------------------------------
 */
elseif ($rec == 'del') {
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'article.php');
    $title = $dou->get_one("SELECT title FROM " . $dou->table('article') . " WHERE id = '$id'");
    
    if (isset($_POST['confirm']) ? $_POST['confirm'] : '') {
        // 删除相应商品图片
        $image = $dou->get_one("SELECT image FROM " . $dou->table('article') . " WHERE id = '$id'");
        $dou->del_image($image);
        
        $dou->create_admin_log($_LANG['article_del'] . ': ' . $title);
        $dou->delete($dou->table('article'), "id = '$id'", 'article.php');
    } else {
        $_LANG['del_check'] = preg_replace('/d%/Ums', $title, $_LANG['del_check']);
        $dou->dou_msg($_LANG['del_check'], 'article.php', '', '30', "article.php?rec=del&id=$id");
    }
} 

/**
 * +----------------------------------------------------------
 * 批量操作选择
 * +----------------------------------------------------------
 */
elseif ($rec == 'action') {
    if (is_array($_POST['checkbox'])) {
        if ($_POST['action'] == 'del_all') {
            // 批量文章删除
            $dou->del_all('article', $_POST['checkbox'], 'id', 'image');
        } elseif ($_POST['action'] == 'category_move') {
            // 批量移动分类
            $dou->category_move('article', $_POST['checkbox'], $_POST['new_cat_id']);
        } else {
            $dou->dou_msg($_LANG['select_empty']);
        }
    } else {
        $dou->dou_msg($_LANG['article_select_empty']);
    }
}

/**
 * +----------------------------------------------------------
 * 首页商品筛选
 * +----------------------------------------------------------
 */
elseif ($rec == 'sort') {
    // act操作项的初始化
    $act = $check->is_rec($_REQUEST['act']) ? $_REQUEST['act'] : '';
 
    if ($act == 'handle') { // 打开筛选功能
        $_SESSION['if_sort'] = $_SESSION['if_sort'] ? false : true;
    } elseif ($act == 'set') { // 设为首页显示商品
        // 验证并获取合法的ID
        $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'article.php');
     
        $max_sort = $dou->get_one("SELECT sort FROM " . $dou->table('article') . " ORDER BY sort DESC");
        $new_sort = $max_sort + 1;
        $dou->query("UPDATE " . $dou->table('article') . " SET sort = '$new_sort' WHERE id = '$id'");
    } elseif ($act == 'cancel') { // 取消首页显示商品
        // 验证并获取合法的ID
        $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'article.php');
        
        $dou->query("UPDATE " . $dou->table('article') . " SET sort = '' WHERE id = '$id'");
    } 
    
    // 跳转到上一页面
    $dou->dou_header($_SERVER['HTTP_REFERER']);
}
/**
 * Ajax 查组织人员
 */
elseif ($rec=='get_parent'){
	
	$cid=intval($_REQUEST['cid']);
	$sql="select * from m_zuzhi where  cid=$cid";
	$res=$GLOBALS['dou']->getAll($sql);
	echo json_encode($res);
}

function get_tree_zuzhi(){
	
	     $sql="select * from m_zuzhi  where cid=1";
	     $res=$GLOBALS['dou']->getAll($sql);
	     
	     foreach ($res as $k=>$v){
	     	
	     	$row=$GLOBALS['dou']->getAll("select * from m_zuzhi  where parent_id={$v['zid']}");

	     	
	     	foreach ($row as $l=>$t){
	     		
	     		$row1=$GLOBALS['dou']->getAll("select * from m_zuzhi  where parent_id={$t['zid']}");
	     		

	     		
	     		foreach ($row1 as $m=>$n){
	     			
	     			$row2=$GLOBALS['dou']->getAll("select * from m_zuzhi  where parent_id={$n['zid']}");
	     			 
	     			
	     			foreach ($row2 as $q=>$w){
	     				
	     				$row3=$GLOBALS['dou']->getAll("select * from m_zuzhi  where parent_id={$w['zid']}");
	     				
	     				
	     				foreach ($row3 as $z=>$x){
	     					
	     					$row4=$GLOBALS['dou']->getAll("select * from m_zuzhi  where parent_id={$x['zid']}");
	     					 
	     					$row3[$z]['child']=$row4;
	     				}
	     				
	     				
	     				$row2[$q]['child']=$row3;
	     			}
	     			
	     			$row1[$m]['child']=$row2;
	     		}
	     		
	     		$row[$l]['child']=$row1;
	     	}
	     	$res[$k]['child']=$row;
	     }
	
	     return $res;
	
}
?>