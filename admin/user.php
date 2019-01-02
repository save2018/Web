<?php
/**
 *  门店管理
 * ------------ --------------------------------------------------------------------------------------
 * 版权所有 2013-2018 ，并保留所有权利。
 * 网站地址: 
 * --------------------------------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在遵守授权协议前提下对程序代码进行修改和使用；不允许对程序代码以任何形式任何目的的再发布。
 * 授权协议：
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
$images_dir = 'images/store/'; // 文件上传路径，结尾加斜杠
$thumb_dir = ''; // 缩略图路径（相对于$images_dir） 结尾加斜杠，留空则跟$images_dir相同
$img = new Upload(ROOT_PATH . $images_dir, $thumb_dir); // 实例化类文件
if (!file_exists(ROOT_PATH . $images_dir)) {
    mkdir(ROOT_PATH . $images_dir, 0777);
}

// 赋值给模板
$smarty->assign('rec', $rec);
$smarty->assign('cur', 'user');

/**
 * +----------------------------------------------------------
 * 门店列表
 * +----------------------------------------------------------
 */
if ($rec == 'default') {
    $smarty->assign('ur_here', $_LANG['user']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['user_add'],
            'href' => 'user.php?rec=add'
    ));
    
    // 获取参数
   // $cat_id = $check->is_number($_REQUEST['cat_id']) ? $_REQUEST['cat_id'] : 0;
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    
    $level=isset($_REQUEST['level']) ? trim($_REQUEST['level']) : 0;
    //echo $keyword;
    // 筛选条件
   $where=' where 1=1 ';
    /*
    $where = ' WHERE cat_id IN (' . $cat_id . $dou->dou_child_id('product_category', $cat_id) . ')';
     */
    if ($keyword) {
    	$phone=base64_encode($keyword);
        $where .=   " AND ( username LIKE '%$keyword%' or phone = '$phone' or user_id like '%$keyword%' )";
        $get = '&keyword=' . $keyword;
    }
    
    if($level !='0'){
    	$where .=" AND  level_code='$level' ";
    }
   // echo $where;
   // exit;
    
   

    // 分页
    $page = $check->is_number($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $page_url = 'user.php' . ('?level='.$level.'&keyword='.$keyword );
    $limit = $dou->pager('user', 15, $page, $page_url, $where, $get);
    
    $sql = "SELECT * FROM " . $dou->table('user') . $where . " ORDER BY id DESC" . $limit;
   // echo $sql;
    $query = $dou->query($sql);
    while ($row = $dou->fetch_array($query)) {
        //$cat_name = $dou->get_one("SELECT cat_name FROM " . $dou->table('product_category') . " WHERE cat_id = '$row[cat_id]'");
        $add_time = date("Y-m-d", $row['add_time']);
             $user_list[] =  array (
                "id" => $row['id'],
                "user_id" => $row['user_id'],
                 "password"=>$row['password'],
                "username" =>$row['username'],
                "code" => $row['code'],
                 "level" => $row['level'],
             	"level_code" => $row['level_code'],
                 "phone" => base64_decode($row['phone']),
        		"up_level" => up_level($row['id']),
                "code_up"=>$row['code_up']
        );
    }
    //print_r($user_list);
    
    // 首页显示商品数量限制框
    for($i = 1; $i <= $_CFG['home_display_product']; $i++) {
        $sort_bg .= "<li><em></em></li>";
    }
    
    // 赋值给模板
  //  $smarty->assign('sort', $dou->get_sort('store', 'name'));
    $smarty->assign('cat_id', $cat_id);
    $smarty->assign('keyword', $keyword);
  //  $smarty->assign('product_category', $dou->get_category_nolevel('product_category'));
    $smarty->assign('user_list', $user_list);
    
    $smarty->display('user.htm');
} 

/**
 * +----------------------------------------------------------
 * 代理商添加
 * +----------------------------------------------------------
 */
elseif ($rec == 'add') {
    $smarty->assign('ur_here', $_LANG['store_add']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['store'],
            'href' => 'store.php' 
    ));
    
    // 格式化自定义参数，并存到数组$product，商品编辑页面中调用商品详情也是用数组$product，

    
    //查询出所有的省份
    $sql="select region_id,region_name from " .$dou->table('region')." where region_type=1";
    $plist=$dou->getAll($sql);
    
    $smarty->assign('plist', $plist);
    
    

    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
    
    // 赋值给模板
    $smarty->assign('form_action', 'insert');
    $smarty->assign('user', $user);
    
    $smarty->display('user.htm');
} 

elseif ($rec == 'insert') {
    // 数据验证
    if (empty($_POST['user_id'])) $dou->dou_msg("ID" . $_LANG['is_empty']);
    if (empty($_POST['password'])) $dou->dou_msg("密码" . $_LANG['is_empty']);
    // 图片上传
    /*
    if ($_FILES['image']['name'] != "") {
        $image_name = $img->upload_image('image', $img->create_file_name('store'));
        $image = $images_dir . $image_name;
        $img->make_thumb($image_name, $_CFG['thumb_width'], $_CFG['thumb_height']);
    }
    */
    
    $add_time = time(); 
    // 格式化自定义参数
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
    
    
    $data['user_id']    =trim($_POST['user_id']);
    $data['password']   =password_hash($_POST['password'],PASSWORD_DEFAULT);
    $data['code']    	=trim($_POST['code']);
    $data['username']   =trim($_POST['username']);
    $data['phone']   	=base64_encode(trim($_POST['phone']));
    $data['level_code'] =trim($_POST['level']);
    $data['code_up'] 	=trim($_POST['code_up']);
    
    $data['update_time'] 	=time();
   // if($_POST['status']) $data['status']=$_POST['status'];

    $dou->autoExecute($dou->table('user') , $data,"INSERT");

    $dou->create_admin_log( '代理商新增: ' . $_POST['user_id']);
    $dou->dou_msg("代理商添加成功", 'user.php');
} 
/**
 * +----------------------------------------------------------
 * AJAX 查询城市
 * +----------------------------------------------------------
 */
elseif ($rec == 'city' || $rec == 'district') {
	
	 $pid = $check->is_number($_REQUEST['pid']) ? $_REQUEST['pid'] : '';
	 if($pid){
	 	
	
	 $sql="select region_id,region_name from " .$dou->table('region')." where parent_id=$pid ";
	 	
	 	
	 $res=$dou->getAll($sql);
	 }
	 

	echo json_encode($res);
	 
}

/**
 * +----------------------------------------------------------
 * 门店编辑
 * +----------------------------------------------------------
 */
elseif ($rec == 'edit') {
    $smarty->assign('ur_here', $_LANG['user_edit']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['user'],
            'href' => 'user.php'
    ));
    
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : '';
   /* 
    $query = $dou->select($dou->table('store'), '*', '`id` = \'' . $id . '\'');
  	$store = $dou->fetch_array($query);
  */
    $sql='select *  from '.$dou->table('user') . ' where `id` = '. $id ;
    $user = $dou->getRow($sql);

	
	$user['phone']=base64_decode($user['phone']);
	//$user['password']=base64_decode($user['phone']);
    // 格式化自定义参数
    if ($_DEFINED['store'] || $product['defined']) {
        $defined = explode(',', $_DEFINED['store']);
        foreach ($defined as $row) {
            $defined_store .= $row . "：\n";
        }
        // 如果商品中已经写入自定义参数则调用已有的
        $user['defined'] = $user['defined'] ? str_replace(",", "\n", $user['defined']) : trim($defined_store);
        $user['defined_count'] = count(explode("\n", $user['defined'])) * 2;
    }
   
    
    //查询组织等级 1 2 3 4 5 下面所有的人员
    

    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());

    // 赋值给模板
    $smarty->assign('form_action', 'update');
    $smarty->assign('user', $user);
    
    $smarty->display('user.htm');
} 
 //更新数据
elseif ($rec == 'update') {
	//print_r($_POST);
    // 数据验证
    if (empty($_POST['username'])) $dou->dou_msg($_LANG['name'] . $_LANG['is_empty']);
   //检查价格 if (!$check->is_price($_POST['price'] = trim($_POST['price']))) $dou->dou_msg($_LANG['price_wrong']);
        
    // 图片上传
   /*
    if ($_FILES['image']['name'] != "") {
        $image_name = $img->upload_image('image', $img->create_file_name('store', $_POST['id'], 'image'));
         $image = $images_dir . $image_name;
        $img->make_thumb($image_name, $_CFG['thumb_width'], $_CFG['thumb_height']);
    }
    */

    
    // 格式化自定义参数
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
   
	$phone=trim($_POST['phone']);

    if($phone){
    	 //后六位为密码
    	$pwd=substr($phone,-6);
    	$data['password']=password_hash($pwd,PASSWORD_DEFAULT);
    }
	
    $data['username'] =$_POST['username'];
    $data['phone']    =base64_encode($phone);
    $data['user_id']  =$_POST['user_id'];
    $data['code']     =$_POST['code'];
    $data['level_code']   =$_POST['level'];
    $data['code_up']  =$_POST['code_up'];
    //$data['password']  =$_POST['password'];

   
    $id=$_POST['id'];
    
    
    //echo "<pre>";
   // print_r($data); 
   // print_r($_FILES['image']);
    //exit;
    $dou->autoExecute($dou->table('user') , $data,"UPDATE", "id=$id");
    //$sql = "update " . $dou->table('store') . " SET cat_id = '$_POST[cat_id]', name = '$_POST[name]', price = '$_POST[price]', defined = '$_POST[defined]' ,content = '$_POST[content]'" . $image . ", keywords = '$_POST[keywords]', description = '$_POST[description]' WHERE id = '$_POST[id]'";
    //$dou->query($sql);
    
    $dou->create_admin_log($_LANG['user_edit'] . ': ' . $_POST['username']);
    $dou->dou_msg($_LANG['user_edit_succes'], 'user.php');
} 

/**
 * +----------------------------------------------------------
 * 重新生成门店图片
 * +----------------------------------------------------------
 */
elseif ($rec == 're_thumb') {
    $smarty->assign('ur_here', $_LANG['product_thumb']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['product'],
            'href' => 'product.php' 
    ));
    
    $sql = "SELECT id, image FROM " . $dou->table('product') . "ORDER BY id ASC";
    $count = mysql_num_rows($query = $dou->query($sql));
    $mask['count'] = preg_replace('/d%/Ums', $count, $_LANG['product_thumb_count']);
    $mask_tag = '<i></i>';
    $mask['confirm'] = $_POST['confirm'];
    
    for($i = 1; $i <= $count; $i++)
        $mask['bg'] .= $mask_tag;
    
    $smarty->assign('mask', $mask);
    $smarty->display('product.htm');
    
    if (isset($_POST['confirm'])) {
        echo ' ';
        while ($row = $dou->fetch_array($query)) {
            $img->make_thumb(basename($row['image']), $_CFG['thumb_width'], $_CFG['thumb_height']);
            echo "<script type=\"text/javascript\">mask('" . $mask_tag . "');</script>";
            flush();
            ob_flush();
        }
        echo "<script type=\"text/javascript\">success();</script>\n</body>\n</html>";
    }
}

/**
 * +----------------------------------------------------------
 * 代理商删除
 * +----------------------------------------------------------
 */
elseif ($rec == 'del') {
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'user.php');
    
    $user_id = $dou->get_one("SELECT user_id FROM " . $dou->table('user') . " WHERE id = '$id'");
    
    if (isset($_POST['confirm']) ? $_POST['confirm'] : '') {
        // 删除相应商品图片
    //    $image = $dou->get_one("SELECT image FROM " . $dou->table('product') . " WHERE id = '$id'");
   //     $dou->del_image($image);
        
        $dou->create_admin_log("删除代理商" . ': ' . $user_id);
        $dou->delete($dou->table('user'), "id = '$id'", 'user.php');
    } else {
        $_LANG['del_check'] = preg_replace('/d%/Ums', $user_id, $_LANG['del_check']);
        $dou->dou_msg($_LANG['del_check'], 'user.php', '', '30', "user.php?rec=del&id=$id");
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
           
            $dou->del_all('user', $_POST['checkbox'], 'id', 'image');
        } elseif ($_POST['action'] == 'category_move') {
            // 批量移动分类
           // $dou->category_move('product', $_POST['checkbox'], $_POST['new_cat_id']);
        } else {
            $dou->dou_msg($_LANG['select_empty']);
        }
    } else {
        $dou->dou_msg("未选择");
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
    } elseif ($act == 'set') { // 设为首页显示文章
        // 验证并获取合法的ID
        $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'product.php');
     
        $max_sort = $dou->get_one("SELECT sort FROM " . $dou->table('product') . " ORDER BY sort DESC");
        $new_sort = $max_sort + 1;
        $dou->query("UPDATE " . $dou->table('product') . " SET sort = '$new_sort' WHERE id = '$id'");
    } elseif ($act == 'cancel') { // 取消首页显示文章
        // 验证并获取合法的ID
        $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'product.php');
        
        $dou->query("UPDATE " . $dou->table('product') . " SET sort = '' WHERE id = '$id'");
    } 
    
    // 跳转到上一页面
    $dou->dou_header($_SERVER['HTTP_REFERER']);
}
/**
 * +----------------------------------------------------------
 * 权限设置
 * +----------------------------------------------------------
 */
elseif ($rec == 'privilege') {


	 $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'user.php');
	
	$user=$dou->getRow("select * from m_user where id=$id");
	
	$smarty->assign('ur_here', $user['username']." > 权限设置");
	$smarty->assign('action_link', array (
			'text' => "返回",
			'href' => $_SERVER['HTTP_REFERER']
				
	));
	
	// CSRF防御令牌生成
	$smarty->assign('token', $firewall->get_token());

	// 赋值给模板
	$smarty->assign('form_action', 'edit_privilege');
	$smarty->assign('user', $user);
	$smarty->display('user_privilege.htm');
}
//保存权限设置
elseif ($rec == 'edit_privilege') {
	
	// 验证并获取合法的ID
	$id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'user.php');
	$data['is_down']=$_POST['is_down'];
	$dou->autoExecute($dou->table('user') , $data,"UPDATE", "id=$id");
	$dou->create_admin_log( '权限设置: ' . $_POST['username']);
	$dou->dou_msg('权限编辑成功', 'user.php');
}




/**
 * excel数据上传
 */
elseif ($rec == 'excel_import'){
	set_time_limit(0);
	/******************导入文件处理*******************/
	$tmp_file = $_FILES['excelData']['tmp_name'];
	$savePath='images/excel/';

	$exchange=$_REQUEST['radioInline'];  //获取radio值


	$file_types = explode(".", $_FILES ['excelData'] ['name']);
	$file_type = $file_types [count($file_types) - 1];
	//print_r($_FILES);exit;
	/*判别是不是.xls xlsx文件，判别是不是excel文件*/
	if (strtolower($file_type) != "xlsx" && strtolower($file_type) != "xls"  && strtolower($file_type) != "csv") {
		$dou->dou_msg('不是Excel或CSV文件,请重新上传');
	}

	if($_FILES ['excelData'] ['size']>0){
		 
		/*以时间来命名上传的文件*/
		$str = date('Ymdhis');
		$file_name = $str . "." . $file_type;
		 
		/*是否上传成功*/
		if (!copy($tmp_file, $savePath . $file_name)) {
			$dou->dou_msg('上传失败');
		}
		 
		//删除所有数据
		/*
		if($exchange=='1'){

			//$sql="DELETE  FROM `m_store` WHERE 1=1";
			$sql="TRUNCATE TABLE m_user";  // 清空所有 id从1开始
			$GLOBALS['dou']->query($sql);
		}
		*/
		 /*
		if(strtolower($file_type)=='csv'){

			importCsv($savePath . $file_name);
			exit;
		}
		*/
		 
		//导入数据
		$excel_data=importExecl($savePath . $file_name);
		 	//echo "<pre>";
			//print_r($excel_data);
			//exit;
		add_excel_user($excel_data);   //保存到数据库
		//print_r($excel_data);
		 
	}else{
		 
		$dou->dou_msg('请检查文件大小');
		 
	}



	//print_r($_FILES);exit;
}

//csv文件处理
function input_csv($handle)
{
	$out = array ();
	$n = 0;
	//php 自带读取csv函数
	while ($data = fgetcsv($handle, 10000))
	{
		$num = count($data);
		for ($i = 0; $i < $num; $i++)
		{
			$out[$n][$i] = $data[$i];
		}
		$n++;
	}
	return $out;
}

function  importCsv($file=''){

	$file = iconv("utf-8", "gb2312", $file);   //转码
	if(empty($file) OR !file_exists($file)) {
		//die('file not exists!');
		$dou->dou_msg('文件不存在');
	}
	/*
	 include('Classes/PHPExcel.php');  //引入PHP EXCEL类

	 $objRead=new PHPExcel_Reader_CSV();

	 if(!$objRead->canRead($file)){

		die('No Csv');
		}
		$obj = $objRead->load($file);  //建立csv对象
		*/
	$handle=fopen($file,'r');
	$result=input_csv($handle);  //解析csv
	//echo "<pre>";
	//print_r($result);
	//exit;
	$len_result=count($result);	
	if($len_result==0){

		$dou->dou_msg('没有任何数据');
	}
	for($i = 1; $i < $len_result; $i++) //循环获取各字段值
	{
		//$name = iconv('gb2312', 'utf-8', $result[$i][0]); //中文转码
		$store_id=$result[$i][0];
		$name=$result[$i][1];
		$phone=$result[$i][3];
		$province=$GLOBALS['dou']->get_one("select region_id from m_region where region_name like'%{$result[$i][4]}%'  and region_type=1");
		$city=$GLOBALS['dou']->get_one("select region_id from m_region where region_name like '%{$result[$i][5]}%'  and region_type=2");;
		//	$district=$GLOBALS['dou']->get_one("select region_id from m_region where region_name='%{$res['G']}%'  and region_type=3");;

		$province=$province;
		$city	 =$city;
		$address=$result[$i][6];
		$baidu_coordinate=$result[$i][7];
		$level1_code=$result[$i][8];
		$level1_name=$result[$i][9];
		$level2_code=$result[$i][10];
		$level2_name=$result[$i][11];
		$level3_code=$result[$i][12];
		$level3_name=$result[$i][13];
		$level4_code=$result[$i][14];
		$level4_name=$result[$i][15];
		$level5_code=$result[$i][16];
		$level5_name=$result[$i][17];

		$api_start_day  =$result[$i][19];
		$api_end_day    =$result[$i][20];
		$api_total_num  =$result[$i][21];
		$api_total_money=$result[$i][22];
		if(strtolower($result[$i][23])=='true'){
			$status	     =1;
		}else{$status	 =0;}

		$add_time=time();

		$data_values .= "('$store_id', '$name', '$phone', '$province', '$city', '$address', '$baidu_coordinate', '$level1_code', '$level1_name', '$level2_code', '$level2_name', '$level3_code', '$level3_name', '$level4_code', '$level4_name', '$level5_code', '$level5_name', '$status', '$api_start_day', '$api_end_day', '$api_total_num', '$api_total_money', '$add_time'),";
	}
	$data_values = substr($data_values,0,-1); //去掉最后一个逗号
	fclose($handle); //关闭指针
	$sql="INSERT INTO `m_store` (store_id, name, phone, province, city, address, baidu_coordinate, level1_code, level1_name, level2_code, level2_name, level3_code, level3_name, level4_code, level4_name, level5_code, level5_name, status, api_start_day, api_end_day, api_total_num, api_total_money, add_time) VALUES $data_values ";
	$query = $GLOBALS['dou']->query($sql); //批量插入数据表中
	if($query)
	{
		$GLOBALS['dou']->dou_msg('导入成功!', 'store.php');
	}else{
		$GLOBALS['dou']->dou_msg('导入失败!', 'store.php');
	}



	//echo "<pre>";
	//print_r($result);
	//exit;


}


/**
 *  excel数据导入
 * @param string $file excel文件
 * @param string $sheet
 * @return string   返回解析数据
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function importExecl($file='', $sheet=0){
	$file = iconv("utf-8", "gb2312", $file);   //转码
	if(empty($file) OR !file_exists($file)) {
		die('file not exists!');
	}
	include('Classes/PHPExcel.php');  //引入PHP EXCEL类
	$objRead = new PHPExcel_Reader_Excel2007();   //建立reader对象
	if(!$objRead->canRead($file)){
		$objRead = new PHPExcel_Reader_Excel5();
		if(!$objRead->canRead($file)){
			die('No Excel!');
		}
	}

	$cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

	$obj = $objRead->load($file);  //建立excel对象
	$currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
	$columnH = $currSheet->getHighestColumn();   //取得最大的列号
	$columnCnt = array_search($columnH, $cellName);
	$rowCnt = $currSheet->getHighestRow();   //获取总行数

	$data = array();
	for($_row=1; $_row<=$rowCnt; $_row++){  //读取内容
		for($_column=0; $_column<=$columnCnt; $_column++){
			$cellId = $cellName[$_column].$_row;
			//$cellValue = $currSheet->getCell($cellId)->getValue();
			$cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值
			if($cellValue instanceof PHPExcel_RichText){   //富文本转换字符串
				$cellValue = $cellValue->__toString();
			}

			$data[$_row][$cellName[$_column]] = $cellValue;
		}
	}

	return $data;
}
/**
 * 将导入的excel数据插入到数据库中
 */
function add_excel_user($arr){
	//删除上面2行
	unset($arr[1]);
	unset($arr[2]);
	//echo "<pre>";
	//print_r($arr);
	foreach ($arr as $k=>$res){
		
		unset($data);

		$data['user_id']=$res['A'];
		
		$level=$res['C'];//级别名称含代码
		
		$result = array(); //提取级别代码
		preg_match_all("/(?:\()(.*)(?:\))/i",$level, $result);
		//echo  $result[1][0];
		$data['level_code']= $result[1][0];
		
		//$province=$GLOBALS['dou']->get_one("select region_id from m_region where region_name like'%{$res['E']}%'  and region_type=1");
		//$city=$GLOBALS['dou']->get_one("select region_id from m_region where region_name like '%{$res['F']}%'  and region_type=2");
		//$district=$GLOBALS['dou']->get_one("select region_id from m_region where region_name='%{$res['G']}%'  and region_type=3");
		$data['username']=$res['D'];
		$data['phone']=base64_encode($res['E']);

		$data['code']=trim($res['B']);
		
		$pw=substr(trim($res['E']),-6);  //截取手机号后6位为密码
		
		$data['password']=password_hash($pw,PASSWORD_DEFAULT);

		$data['code_up']  =$res['F']?$res['F']:'';

		$data['update_time']=time();
		//echo "<pre>";

		$user_id=$data['user_id'];
		$row=$GLOBALS['dou']->getRow("select id from m_user where user_id='$user_id'");
		//print_r($data);
		//exit;
		if($row['id']){
			unset($data['user_id']);
			//更新
			$GLOBALS['dou']->autoExecute($GLOBALS['dou']->table('user') ,$data,"UPDATE"," user_id='$user_id'");
		}else{
			//新增
			$GLOBALS['dou']->autoExecute($GLOBALS['dou']->table('user') ,$data,"INSERT");
		}


	}
	$GLOBALS['dou']->dou_msg('导入成功!', 'user.php');
}

/**
 * 通过用户id查询上级代理  返回所有的代理结果集
 */
function  up_level($uid){
		
		
	$rt=array();
	$dou=$GLOBALS['dou'];
	$sql='select id,username,code_up from '.$dou->table('user') . ' where `id` = '. $uid ;

	$res = $dou->getRow($sql);

	$rt[]=$res;
	if($res['code_up']){

		$sql="select id,username,code_up from ".$dou->table('user') . " where `code` =  {$res['code_up']}";
		$t1 = $dou->getRow($sql);
		$rt[]=$t1;
		if($t1['code_up']) {
			$sql="select id,username,code_up from ".$dou->table('user') . " where `code` =  {$t1['code_up']}";
			$t2=$dou->getRow($sql);

			$rt[]=$t2;
			if($t2['code_up']){
				$sql="select id,username,code_up from ".$dou->table('user') . " where `code` =  {$t2['code_up']}";
				//$t3=$this->getORM()->select('id','username','code_up')->where('code',$t2['code_up'])->fetchOne();
				$t3=$dou->getRow($sql);
				$rt[]=$t3;
				
				if($t3['code_up']){
					
					$sql="select id,username,code_up from ".$dou->table('user') . " where `code` =  {$t3['code_up']}";
					$t4=$dou->getRow($sql);
					$rt[]=$t4;
				}
			}
		}
	}
	return  array_reverse($rt); //顺序反转，上级从大到小


}










?>