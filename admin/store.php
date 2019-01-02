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
//$images_dir = 'images/store/'; // 文件上传路径，结尾加斜杠
$images_dir = 'api/public/uploads/'; // 文件上传路径，结尾加斜杠
$thumb_dir = ''; // 缩略图路径（相对于$images_dir） 结尾加斜杠，留空则跟$images_dir相同
$img = new Upload(ROOT_PATH . $images_dir, $thumb_dir); // 实例化类文件
if (!file_exists(ROOT_PATH . $images_dir)) {
    mkdir(ROOT_PATH . $images_dir, 0777);
}

// 赋值给模板
$smarty->assign('rec', $rec);
$smarty->assign('cur', 'store');

/**
 * +----------------------------------------------------------
 * 门店列表
 * +----------------------------------------------------------
 */
if ($rec == 'default') {
    $smarty->assign('ur_here', $_LANG['store']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['store_add'],
            'href' => 'store.php?rec=add' 
    ));
    
    // 获取参数
    $changwu_id = $check->is_number($_REQUEST['changwu']) ? $_REQUEST['changwu'] : 0;
    
    $lishi_id = $check->is_number($_REQUEST['lishi']) ? $_REQUEST['lishi'] : 0;
    
    $shengdai_id = $check->is_number($_REQUEST['shengdai']) ? $_REQUEST['shengdai'] : 0;
    
    $shidai_id = $check->is_number($_REQUEST['shidai']) ? $_REQUEST['shidai'] : 0;
    
    $xiandai_id = $check->is_number($_REQUEST['xiandai']) ? $_REQUEST['xiandai'] : 0;
    
    $type_id = $check->is_number($_REQUEST['type_id']) ? $_REQUEST['type_id'] : 0;

    $auditing_status=$_REQUEST['auditing_status'] ;
    
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';

    //echo $shengdai_id."PPP";
    //echo $keyword;
    $where='  where 1=1 ';
    $get ='';
    if($keyword){
    	$phone=base64_encode($keyword);
        $where .=   " AND ( name LIKE '%$keyword%' or phone = '$phone' or store_name like '%$keyword%' )";
        // '&keyword=' . $keyword;
    }
    //如果存在县id 
    if($xiandai_id){
    	
    	$where .=" AND uid=$xiandai_id";
    }
    	//如果存在市代理id
    if($shidai_id){
    		
    		//通过市代理uid 查询出县代理uid

    		$uidstr=loshidai($shidai_id);
    		
    		
    		$where .=" AND uid in($uidstr)";
    		
    		
    }
    if ($shengdai_id){
    		//echo  $shengdai_id;
    		//先查市代
    		$uidstr=loshengdai($shengdai_id);
    		
    		$where .=" AND uid in($uidstr)";
    }if($lishi_id){
    		
    	$uidstr=lolishi($lishi_id);
    	 
    	$where .=" AND uid in($uidstr)";
    		
    }if ($changwu_id){
    		
    		
    	$uidstr=lochangwu($changwu_id);
    	
    	$where .=" AND uid in($uidstr)";
   	}
   	
   	if($type_id){
   		
   		$where .=" AND type_id=$type_id";
   		
   	}
    	//echo $auditing_status."AAAAA";
    $page_str='';
    if(isset($auditing_status) &&$auditing_status!=-1 ){

        $where .="  AND auditing_status=$auditing_status ";
        $page_str="&auditing_status=$auditing_status";
    }
    //echo $where;

    // 分页
    $page = $check->is_number($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $page_url = 'store.php' . ("?changwu=$changwu_id&lishi=$lishi_id&shengdai=$shengdai_id&shidai=$shidai_id&xiandai=$xiandai_id&type_id=$type_id&keyword=$keyword".$page_str);
    $limit = $dou->pager('store', 15, $page, $page_url, $where, $get);
    
    $sql = "SELECT * FROM " . $dou->table('store') . $where . " ORDER BY id DESC" . $limit;
   // echo $sql;
    $query = $dou->query($sql);
    while ($row = $dou->fetch_array($query)) {
        //$cat_name = $dou->get_one("SELECT cat_name FROM " . $dou->table('product_category') . " WHERE cat_id = '$row[cat_id]'");
        $add_time = date("Y-m-d", $row['add_time']);
        $province=$dou->get_one("select region_name from " . $dou->table('region'). " where region_id='$row[province]'");
        $city=$dou->get_one("select region_name from " . $dou->table('region'). " where region_id='$row[city]'");
        $district=$dou->get_one("select region_name from " . $dou->table('region'). " where region_id='$row[district]'");
        $store_list[] =  array (
                "id" => $row['id'],
                "store_id" => $row['store_id'],
                "store_name" => $row['store_name'],
                "name" => $row['name'],
        		"phone" => @base64_decode($row['phone']),
               // "add_time" => $add_time,
        		"province"=>$province,
        		"city"=>$city,
        		"district"=>$district,
				'xy_axis'=>$row['xy_axis'],
				'type_id'=>$row['type_id'],
				'code_up'=>$row['code_up'],
        		'update_time'=>$row['update_time']>0 ? date("m.d-H:i",$row['update_time']):0,
        		'add_time'=>$row['add_time']>0 ? date("m.d-H:i",$row['add_time']):0 ,
        		//"status"=>$row['status'],
				"up_level"=>up_level($row['uid']),//上级代理
				"username"=>$dou->get_one("SELECT username FROM " . $dou->table('user') . " WHERE id = '$row[uid]'"),
				"nickName"=>$dou->get_one("SELECT nickName FROM " . $dou->table('wechat') . " WHERE openid = '$row[openid]'"),
        		'auditing_status'=>$row['auditing_status']
        );
    }
	//echo "<pre>";
    //print_r($store_list);
    
    // 首页显示商品数量限制框
    for($i = 1; $i <= $_CFG['home_display_product']; $i++) {
        $sort_bg .= "<li><em></em></li>";
    }
	
	//select 框值
	//查询常务
	
	$changwu=$dou->getAll("SELECT id,username,code FROM " . $dou->table('user') . " WHERE  level_code='J' order by username desc");
	//echo "<pre>";
	//print_r($changwu);
	$smarty->assign('changwu', $changwu);
	//理事
	$lishi=$dou->getAll("SELECT id,username,code FROM " . $dou->table('user') . " WHERE level_code='L' order by username desc");

	$smarty->assign('lishi', $lishi);
	
	//省代
	$shengdai=$dou->getAll("SELECT id,username,code FROM " . $dou->table('user') . " WHERE level_code='D' order by username desc");
	
	$smarty->assign('shengdai', $shengdai);
	
	//市代
	$shidai=$dou->getAll("SELECT id,username,code FROM " . $dou->table('user') . " WHERE level_code='B' order by username desc");
	
	$smarty->assign('shidai', $shidai);
	
	//县代
	$xiandai=$dou->getAll("SELECT id,username,code FROM " . $dou->table('user') . " WHERE level_code='G' order by username desc ");
	
	$smarty->assign('xiandai', $xiandai);
	//echo "<pre>";
	//print_r($xiandai);
	
    
    // 赋值给模板
  //  $smarty->assign('sort', $dou->get_sort('store', 'name'));
    $smarty->assign('cat_id', $cat_id);
    $smarty->assign('keyword', $keyword);
  //  $smarty->assign('product_category', $dou->get_category_nolevel('product_category'));
    $smarty->assign('store_list', $store_list);
    
    $smarty->display('store.htm');
} 

/**
 * +----------------------------------------------------------
 * 门店添加
 * +----------------------------------------------------------
 */
elseif ($rec == 'add') {
    $smarty->assign('ur_here', $_LANG['store_add']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['store'],
            'href' => 'store.php' 
    ));
    
    // 格式化自定义参数，并存到数组$product，商品编辑页面中调用商品详情也是用数组$product，
    if ($_DEFINED['product']) {
        $defined = explode(',', $_DEFINED['product']);
        foreach ($defined as $row) {
            $defined_product .= $row . "：\n";
        }
        $product['defined'] = trim($defined_product);
        $product['defined_count'] = count(explode("\n", $product['defined'])) * 2;
    }
    
    //查询出所有的省份
    $sql="select region_id,region_name from " .$dou->table('region')." where region_type=1";
    $plist=$dou->getAll($sql);
    
    $smarty->assign('plist', $plist);
    
    
    //查询组织等级 1 2 3 4 5 下面所有的人员
    /*
    $level=array();
    $level['level1']=$dou->getAll("select * from m_zuzhi where cid=1");
    $level['level2']=$dou->getAll("select * from m_zuzhi where cid=2");
    $level['level3']=$dou->getAll("select * from m_zuzhi where cid=3");
    $level['level4']=$dou->getAll("select * from m_zuzhi where cid=4");
    $level['level5']=$dou->getAll("select * from m_zuzhi where cid=5");
    // print_r($level['levle1']);
    $smarty->assign('levels', $level);
    */
    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
    
    // 赋值给模板
    $smarty->assign('form_action', 'insert');
  //  $smarty->assign('product_category', $dou->get_category_nolevel('product_category'));
    $smarty->assign('store', $store);
    
    $smarty->display('store.htm');
} 

elseif ($rec == 'insert') {
    // 数据验证
    if (empty($_POST['name'])) $dou->dou_msg($_LANG['name'] . $_LANG['is_empty']);  
    // 图片上传
    if ($_FILES['image']['name'] != "") {
        $image_name = $img->upload_image('image', $img->create_file_name('store'));
        $image = str_replace('api/','',$images_dir) . $image_name;  //为了保持和小程序上传路径一致 去掉api/存储
       // $img->make_thumb($image_name, $_CFG['thumb_width'], $_CFG['thumb_height']); 缩略图
        $data['image_url']   =$image;
    }
    
    $add_time = time(); 
    // 格式化自定义参数
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
    
    
    $data['store_name']    =$_POST['store_name'];
    $data['name']    =$_POST['name'];
    $data['phone']   =$_POST['phone'];
    $data['province']=$_POST['province'];
    $data['city']    =$_POST['city'];
    $data['district']=$_POST['district'];
    $data['address'] =$_POST['address'];
    $data['type_id'] =$_POST['type_id'];
    $data['xy_axis'] =$_POST['xy_axis'];
    /*
    $data['level1_code'] =$_POST['level1_code'];
    $data['level2_code'] =$_POST['level2_code'];
    $data['level3_code'] =$_POST['level3_code'];
    $data['level4_code'] =$_POST['level4_code'];
    $data['level5_code'] =$_POST['level5_code'];
    
    $data['level1_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level1_code']}'");
    $data['level2_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level2_code']}'");
    $data['level3_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level3_code']}'");
    $data['level4_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level4_code']}'");
    $data['level5_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level5_code']}'");
    
    
    
    $data['image']   =$image;
    $data['api_end_day']    =$_POST['api_end_day'];
    $data['api_total_num']  =$_POST['api_total_num'];
    $data['api_total_money']=$_POST['api_total_money'];
    $data['remark']			=$_POST['remark'];
    */
    if($_POST['status']) $data['status']=$_POST['status'];
     

    
	$data['xy_axis']=$_POST['xy_axis'];
	$data['type_id']=$_POST['type_id'];
  //  echo "<pre>";
    // print_r($data);
    // print_r($_FILES['image']);
   // exit;
    $dou->autoExecute($dou->table('store') , $data,"INSERT");

    
    
    $dou->create_admin_log($_LANG['store_add'] . ': ' . $_POST['name']);
    $dou->dou_msg($_LANG['store_add_succes'], 'store.php');
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
 * AJAX 查询下一等级
 * +----------------------------------------------------------
 */

 elseif ($rec == 'level_next' ) {
	
	 $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : '';
	 if($id){
	 	
	
	 $sql="select id,username,code from " .$dou->table('user')." where id=$id ";
	 	
	 $res=$dou->getRow($sql);
	 	
	 $sql="select id,username,code,level_code,code_up from ".$dou->table('user') . " where `code_up` =  {$res['code']}";
	
	 $result=$dou->getAll($sql);
	 }else{
		 
		 $result['msg']=0;
	 }
	 

	echo json_encode($result);
	 
}

/**
 *审核
 */
elseif ($rec=='shenhe'){

      $e=array();
    $id=$_REQUEST['id'];

    $data['auditing_status']=1;
    $res=$dou->autoExecute($dou->table('store') , $data,"UPDATE", "id=$id");
    if($res){

        $e['code']=1;
    }else{
        $e['code']=0;
    }
    echo json_encode($e);
}

/**
 * +----------------------------------------------------------
 * 门店编辑
 * +----------------------------------------------------------
 */
elseif ($rec == 'edit') {
    $smarty->assign('ur_here', $_LANG['store_edit']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['store'],
            'href' => 'store.php' 
    ));
    
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : '';
   /* 
    $query = $dou->select($dou->table('store'), '*', '`id` = \'' . $id . '\'');
  	$store = $dou->fetch_array($query);
  */
    $sql='select *  from '.$dou->table('store') . ' where `id` = '. $id ;
    $store = $dou->getRow($sql);

	$province=$dou->get_one("select region_name from " . $dou->table('region'). " where region_id='$store[province]'");
	$city=$dou->get_one("select region_name from " . $dou->table('region'). " where region_id='$store[city]'");
	$district=$dou->get_one("select region_name from " . $dou->table('region'). " where region_id='$store[district]'");

    $store['phone']=@base64_decode($store['phone']);

	$store['province_name']=$province;
	$store['city_name']=$city;
	$store['district_name']=$district;
	
	//查询出所有的省份
	$sql="select region_id,region_name from " .$dou->table('region')." where region_type=1";
    $plist=$dou->getAll($sql);
	 
	$smarty->assign('plist', $plist);
	
	//当前省份下 城市
	
	$sql="select region_id,region_name from " .$dou->table('region')." where parent_id={$store['province']}";
	$clist=$dou->getAll($sql);
	$smarty->assign('clist', $clist);
	
	//当前城下所有地区
	
	$sql="select region_id,region_name from " .$dou->table('region')." where parent_id={$store['city']}";
	$dlist=$dou->getAll($sql);
	$smarty->assign('dlist', $dlist);
	

   // print_r($slist);
    // 格式化自定义参数
    if ($_DEFINED['store'] || $product['defined']) {
        $defined = explode(',', $_DEFINED['store']);
        foreach ($defined as $row) {
            $defined_store .= $row . "：\n";
        }
        // 如果商品中已经写入自定义参数则调用已有的
        $store['defined'] = $store['defined'] ? str_replace(",", "\n", $store['defined']) : trim($defined_store);
        $store['defined_count'] = count(explode("\n", $store['defined'])) * 2;
    }
   
   //查询当前‘区’下面所有的门店信息（如果地区不存在就查 ‘市’）
    if($store[district]){
        $sql="select *  from ".$dou->table('store') . " where district='$store[district]' and id !=$store[id]";
    }else{

        $sql="select *  from ".$dou->table('store') . " where city='$store[city]' and id !=$store[id]";
    }
    $dist=$dou->getAll($sql);
    foreach ($dist as $k=>$v){
        if($v['phone']){
            $dist[$k]['phone']=base64_decode($v['phone']);
            $dist[$k]['store_type']= ($v['type_id']==1)?'维娜店铺':'SPA店铺';
        }
        $dist[$k]['up_level']=up_level($v['uid']);

    }
   // echo "<pre>";
   // print_r($dist);

    $smarty->assign('dist', json_encode($dist)); //转为json传给js

    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
   // echo "<pre>";
   // print_r($store);
    // 赋值给模板
    $smarty->assign('form_action', 'update');
  //  $smarty->assign('product_category', $dou->get_category_nolevel('product_category'));
    $smarty->assign('store', $store);
    
    $smarty->display('store.htm');
} 
 //更新数据
elseif ($rec == 'update') {
    // 数据验证
    if (empty($_POST['name'])) $dou->dou_msg($_LANG['name'] . $_LANG['is_empty']);
   //检查价格 if (!$check->is_price($_POST['price'] = trim($_POST['price']))) $dou->dou_msg($_LANG['price_wrong']);
        
    // 图片上传
   
    if ($_FILES['image']['name'] != "") {
        $image_name = $img->upload_image('image', $img->create_file_name('store', $_POST['id'], 'image'));
         $image = str_replace('api/','',$images_dir) . $image_name;
        //$img->make_thumb($image_name, $_CFG['thumb_width'], $_CFG['thumb_height']); 生成缩略图

         $data['image_url']   =$image;
    }

    
    // 格式化自定义参数
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
    

    $data['store_name']    =$_POST['store_name']; 
    $data['name']    =$_POST['name'];
    $data['phone']   =base64_encode($_POST['phone']);
    $data['province']=$_POST['province'];
    $data['city']    =$_POST['city'];
    $data['district']=$_POST['district'];
    $data['address'] =$_POST['address'];
	$data['type_id']=$_POST['type_id'];
	$data['xy_axis']=preg_replace('# #','',trim($_POST['xy_axis'])); //去除空格
	$data['update_time']=time();
	$data['auditing_status']=$_POST['auditing_status'];
	/*
    $data['level1_code'] =$_POST['level1_code'];
    $data['level2_code'] =$_POST['level2_code'];
    $data['level3_code'] =$_POST['level3_code'];
    $data['level4_code'] =$_POST['level4_code'];
    $data['level5_code'] =$_POST['level5_code'];
    
    $data['level1_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level1_code']}'");
    $data['level2_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level2_code']}'");
    $data['level3_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level3_code']}'");
    $data['level4_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level4_code']}'");
    $data['level5_name']=$dou->get_one("select zname from m_zuzhi where code='{$data['level5_code']}'");
    */
    
 
   // $data['api_end_day']    =$_POST['api_end_day'];
   // $data['api_total_num']  =$_POST['api_total_num'];
   // $data['api_total_money']=$_POST['api_total_money'];
   // $data['baidu_coordinate']=$_POST['baidu_coordinate'];
    $data['remark']=$_POST['remark'];
    if($_POST['status']) $data['status']=$_POST['status'];
   
    $id=$_POST['id'];
    
    
    //echo "<pre>";
   // print_r($data); 
   // print_r($_FILES['image']);
    //exit;
    $dou->autoExecute($dou->table('store') , $data,"UPDATE", "id=$id");
    //$sql = "update " . $dou->table('store') . " SET cat_id = '$_POST[cat_id]', name = '$_POST[name]', price = '$_POST[price]', defined = '$_POST[defined]' ,content = '$_POST[content]'" . $image . ", keywords = '$_POST[keywords]', description = '$_POST[description]' WHERE id = '$_POST[id]'";
    //$dou->query($sql);
    
    $dou->create_admin_log($_LANG['store_edit'] . ': ' . $_POST['name']);
    $dou->dou_msg($_LANG['store_edit_succes'], 'store.php');
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
 * 门店删除
 * +----------------------------------------------------------
 */
elseif ($rec == 'del') {
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'product.php');
    
    $name = $dou->get_one("SELECT name FROM " . $dou->table('store') . " WHERE id = '$id'");
    
    if (isset($_POST['confirm']) ? $_POST['confirm'] : '') {
        // 删除相应商品图片
    //    $image = $dou->get_one("SELECT image FROM " . $dou->table('product') . " WHERE id = '$id'");
   //     $dou->del_image($image);
        
        $dou->create_admin_log("删除门店" . ': ' . $name);
        $dou->delete($dou->table('store'), "id = '$id'", 'store.php');
    } else {
        $_LANG['del_check'] = preg_replace('/d%/Ums', $name, $_LANG['del_check']);
        $dou->dou_msg($_LANG['del_check'], 'store.php', '', '30', "store.php?rec=del&id=$id");
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
            // 批量删除门店  //同时删除图片
            $dou->del_all('store', $_POST['checkbox'], 'id', 'image');
        } elseif ($_POST['action'] == 'category_move') {
            // 批量移动分类
            $dou->category_move('product', $_POST['checkbox'], $_POST['new_cat_id']);
        } else {
            $dou->dou_msg($_LANG['select_empty']);
        }
    } else {
        $dou->dou_msg($_LANG['product_select_empty']);
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
    	if($exchange=='1'){
    		
    		//$sql="DELETE  FROM `m_store` WHERE 1=1";
    		$sql="TRUNCATE TABLE m_store";  // 清空所有 id从1开始
    		$GLOBALS['dou']->query($sql);
    	}
    	
    	if(strtolower($file_type)=='csv'){
    		
    		importCsv($savePath . $file_name);
    		exit;
    	}
    	
    	//导入数据
    	$excel_data=importExecl($savePath . $file_name);  	
    	
    //	print_r($excel_data);
    //	exit;
    	add_excel_store($excel_data);   //保存到数据库
    	//print_r($excel_data);
    	
    }else{
    	
    	$dou->dou_msg('请检查文件大小');
    	
    }
    
    
    
	//print_r($_FILES);exit;
}
/**
 * Ajax 查组织下级
 */
elseif ($rec=='get_level'){

	$zcode=trim($_REQUEST['zid']);
	
	//当前级别  1 2 3 4 5
  //$cid=$GLOBALS['dou']->get_one("select cid from m_zuzhi where zid=$zid");
	 //$sql="select zid from m_zuzhi where code='$zcode'";
	// echo $sql;
	$id=$GLOBALS['dou']->get_one("select zid from m_zuzhi where code='$zcode'");
	
	//查询当前级别下一级 所有的人员
	$sql="select * from m_zuzhi where  parent_id=$id";
	$res=$GLOBALS['dou']->getAll($sql);
	echo json_encode($res);
}
elseif($rec=='check_coordinate'){
	
	$sql="select * from m_store where id={$_REQUEST['id']}";
	
	$res=$GLOBALS['dou']->getRow($sql);
	
	if($res['baidu_coordinate']!=''){
		
		 $json_data['msg']=0;

	}else{
		
		$address=$res['address'];
		$gaode="https://restapi.amap.com/v3/place/text?s=rsv3&children=&key=8325164e247e15eea68b59e89200988b&page=1&offset=10&city=310000&language=zh_cn&callback=jsonp_102442_&platform=JS&logversion=2.0&sdkversion=1.3&appname=https%3A%2F%2Flbs.amap.com%2Fconsole%2Fshow%2Fpicker&csid=8392EB07-A84F-4B92-A562-BC2B2B70A3D2&keywords=";
		
		$json=@GoCurl($gaode, $address, 'GET');

		$cont=explode(':',$json);
		$zuoarr=explode('"',$cont[15]);
	
		if($zuoarr[1]){
	
			$gaode_zuobiao=$zuoarr[1];
			
			$xy=explode(",",$gaode_zuobiao);
			$x=$xy[0];
			$y=$xy[1];
			$ty=bd_encrypt($x,$y);
			
			if($ty){
			
				$zuobiao=$ty['bd_lon'].",".$ty['bd_lat'];
	
				$sql="update m_store set baidu_coordinate='$zuobiao',gaode_coordinate='$gaode_zuobiao' where id={$_REQUEST['id']} ";
				$GLOBALS['dou']->query($sql);
			}
			$json_data['msg']=1;
			$json_data['data']='查询成功';
			
		}else{
			$json_data['msg']=-1;
			$json_data['data']="服务器连接失败,请稍后重试";
			
		}
	}
	echo json_encode($json_data);
	exit;

	
	
	
}

/**
 * 通过常务id查询所有的县代理id
 * @param unknown $id
 */
function  lochangwu($id){

	$dou=$GLOBALS['dou'];
	$sql="select code from " .$dou->table('user')." where id=$id";//常务code
	$code=$dou->get_one($sql);//
	$sql="select id,code,code_up from " .$dou->table('user')." where code_up=$code";//
	$lishi=$dou->getAll($sql);

	foreach ($lishi as $k=>$v){

		$str .=lolishi($v['id']).',';
	}
	return  rtrim($str,',');
}


/**
 * 通过理事id查询所有的县代理id
 * @param unknown $id
 */
function  lolishi($id){
	
	$dou=$GLOBALS['dou'];
	$sql="select code from " .$dou->table('user')." where id=$id";//理事code
	$code=$dou->get_one($sql);//理事code
	$sql="select id,code,code_up from " .$dou->table('user')." where code_up=$code";//
	$shengdai=$dou->getAll($sql);
	
	foreach ($shengdai as $k=>$v){
		
		$str .=loshengdai($v['id']).',';
	}
	return  rtrim($str,',');
}

/**
 * 通过省代理id查询所有的县代理id
 * @param unknown $id
 */
function loshengdai($id){
	$dou=$GLOBALS['dou'];
	$sql="select code from " .$dou->table('user')." where id=$id";
	$code=$dou->get_one($sql);//省代编码
	
	$sql="select id,code,code_up from " .$dou->table('user')." where code_up=$code";  //查询市代
	
	$shidai=$dou->getAll($sql);
	//print_r($shidai);
	$str='';
	foreach ($shidai as $k=>$v){
		
		$str .=loshidai($v['id']).',';
		
	}
	//echo $str;
	return  rtrim($str,',');//删除最后一个;
}


/**
 * 通过市代理id查询下级县代理id
 * @param unknown $id
 * @return 逗号相隔字符串
 */
function loshidai($id){
	$dou=$GLOBALS['dou'];
	$sql="select code from " .$dou->table('user')." where id=$id";
	$code=$dou->get_one($sql);//
	
	//
	$sql="select id from " .$dou->table('user')." where code_up='$code'";
	$res=$dou->getAll($sql);
	$string='';
	foreach ($res as $k=>$v){
		
				$string.=$v['id'].',';	
	}

	return  rtrim($string,',');//删除最后一个,
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
function add_excel_store($arr){
     //删除上面1行
	unset($arr[1]);
	//unset($arr[2]);

	foreach ($arr as $k=>$res){

	unset($data);
	
	$data['store_id']=$res['A'];
	$data['name']=$res['B'];
	$data['phone']=$res['D'];
	$province=$GLOBALS['dou']->get_one("select region_id from m_region where region_name like'%{$res['E']}%'  and region_type=1");
	$city=$GLOBALS['dou']->get_one("select region_id from m_region where region_name like '%{$res['F']}%'  and region_type=2");;
//	$district=$GLOBALS['dou']->get_one("select region_id from m_region where region_name='%{$res['G']}%'  and region_type=3");;
	
	
	$data['province']=$province;
	$data['city']	 =$city;
//	$data['district']=$district;
	
	$data['address']=$res['G'];
	$data['baidu_coordinate']=$res['H'];
	$data['level1_code']=$res['I'];
	$data['level1_name']=$res['J'];
	$data['level2_code']=$res['K'];
	$data['level2_name']=$res['L'];
	$data['level3_code']=$res['M'];
	$data['level3_name']=$res['N'];
	$data['level4_code']=$res['O'];
	$data['level4_name']=$res['P'];
	$data['level5_code']=$res['Q'];
	$data['level5_name']=$res['R'];
	

	$data['api_start_day']  =$res['T'];
	$data['api_end_day']    =$res['U'];
	$data['api_total_num']  =$res['V'];
	$data['api_total_money']=$res['W'];
	$data['status']	     =$res['X'];
	$data['add_time']=time();
	//echo "<pre>";
	//print_r($data);
	$store_id=$data['store_id'];
	$row=$GLOBALS['dou']->getRow("select * from m_store where store_id='$store_id'");
	if($row['id']){
		unset($data['store_id']);
		//更新
		$GLOBALS['dou']->autoExecute($GLOBALS['dou']->table('store') ,$data,"UPDATE"," store_id='$store_id'");
	}else{
		//新增
		$GLOBALS['dou']->autoExecute($GLOBALS['dou']->table('store') ,$data,"INSERT");
	}


	}
	$GLOBALS['dou']->dou_msg('导入成功!', 'store.php');
}


//GCJ-02(火星，高德)坐标转换成BD-09(百度)坐标
//@param bd_lon 百度经度
//@param bd_lat 百度纬度
function bd_encrypt($gg_lon,$gg_lat){
	$x_pi = 3.14159265358979324 * 3000.0 / 180.0;
	$x = $gg_lon;
	$y = $gg_lat;
	$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
	$theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
	$bd_lon = $z * cos($theta) + 0.0065;
	$bd_lat = $z * sin($theta) + 0.006;
	// 保留小数点后六位
	$data['bd_lon'] = round($bd_lon, 6);
	$data['bd_lat'] = round($bd_lat, 6);
	return $data;
}
/**
 * curl 函数
 * @param string $url 请求的地址
 * @param string $type POST/GET/post/get
 * @param array $data 要传输的数据
 * @param string $err_msg 可选的错误信息（引用传递）
 * @param int $timeout 超时时间
 * @param array 证书信息
 * @author 勾国印
 */
function GoCurl($url, $data = false,$type, &$err_msg = null, $timeout = 60, $cert_info = array())
{
	$type = strtoupper($type);
	if ($type == 'GET' && is_array($data)) {
		$data = http_build_query($data);
	}

	$option = array();

	if ( $type == 'POST' ) {
		$option[CURLOPT_POST] = 1;
	}
	if ($data) {
		if ($type == 'POST') {
			$option[CURLOPT_POSTFIELDS] = $data;
		} elseif ($type == 'GET') {
			$url = strpos($url, '?') !== false ? $url.'&'.$data :  $url.'?'.$data;
		}
	}

	$option[CURLOPT_URL]            = $url;
	$option[CURLOPT_FOLLOWLOCATION] = TRUE;
	$option[CURLOPT_MAXREDIRS]      = 4;
	$option[CURLOPT_RETURNTRANSFER] = TRUE;
	$option[CURLOPT_TIMEOUT]        = $timeout;

	//设置证书信息
	if(!empty($cert_info) && !empty($cert_info['cert_file'])) {
		$option[CURLOPT_SSLCERT]       = $cert_info['cert_file'];
		$option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
		$option[CURLOPT_SSLCERTTYPE]   = $cert_info['cert_type'];
	}

	//设置CA
	if(!empty($cert_info['ca_file'])) {
		// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
		$option[CURLOPT_SSL_VERIFYPEER] = 1;
		$option[CURLOPT_CAINFO] = $cert_info['ca_file'];
	} else {
		// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
		$option[CURLOPT_SSL_VERIFYPEER] = 0;
	}

	$ch = curl_init();
	curl_setopt_array($ch, $option);
	$response = curl_exec($ch);
	$curl_no  = curl_errno($ch);
	$curl_err = curl_error($ch);
	curl_close($ch);

	// error_log
	if($curl_no > 0) {
		if($err_msg !== null) {
			$err_msg = '('.$curl_no.')'.$curl_err;
		}
	}
	return $response;
}

?>