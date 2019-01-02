<?php
/**
 * Web Map
 * --------------------------------------------------------------------------------------------------
 * 版权所有 2013-2018 网络科技有限公司，并保留所有权利。
 * 网站地址: 
 * --------------------------------------------------------------------------------------------------
 * 
 * --------------------------------------------------------------------------------------------------
 * Author: Save
 * Release Date: 2018-06-22
 */
define('IN_DOUCO', true);
set_time_limit(0);
ini_set('memory_limit', '512M');
// 强制在移动端中显示PC版
if (isset($_REQUEST['mobile'])) {
    setcookie('client', 'pc');
    if ($_COOKIE['client'] != 'pc') $_COOKIE['client'] = 'pc';
}

require (dirname(__FILE__) . '/include/init.php');

// 如果存在搜索词则转入搜索页面
/*
if ($_REQUEST['s']) {
    if ($check->is_search_keyword($keyword = trim($_REQUEST['s']))) {
        require (ROOT_PATH . 'include/search.inc.php');
    } else {
        $dou->dou_msg($_LANG['search_keyword_wrong']);
    }
}
*/
$smarty->assign('theme_name', $_CFG['site_theme']);

/*------------------------------------------------------ */
//-- 框架
/*------------------------------------------------------ */
if ($_REQUEST['act'] == '')
{ 
	//调用组织信息
	$zuzhi=get_zuzhi();
	//所有门店信息
	$store_list=get_Allstore();
	
	$smarty->assign('store_list', $store_list['data']);
	/* 取得国家列表、商店所在国家、商店所在国家的省列表 */
	$country_list=get_regions();
	$smarty->assign('country_list',       $country_list);
	$smarty->assign('shop_country',       1);
	$shop_province_list=get_regions(1, 1);
	
	foreach ($shop_province_list as $key=>$val){
	
		$city=get_regions(2, $val['region_id']);
	
		$shop_province_list[$key]['city']=$city;
	
		foreach ($city as $k=>$v){
	
			$districts=get_regions(3, $v['region_id']);
			$shop_province_list[$key]['city'][$k]['district']=$districts;
	
				
		}
	
	
	}
	$smarty->assign('shop_province_list', $shop_province_list);
	
	$smarty->assign('zuzhi', $zuzhi);
	
	//所有的门店 不分页
	//$all_store=get_Allstore('all');
	
//	$smarty->assign('all_store', json_encode($all_store['data']));  //所有的数据
	
//	$smarty->assign('money_all', $all_store['money_heji']);
	 // 查询组织1等级的编号
	$sql="SELECT level1_code , level1_name FROM `m_store`  WHERE level1_code!=''  GROUP BY level1_code";
	
	$level1=$GLOBALS['dou']->getAll($sql);
	
	//print_r($level1);
	
	foreach ($level1 as $key=>$val){
		
		$baidu_coordinate[]=getAll_baidu_coordinate($val['level1_code']);
	}
	//echo "<pre>";
	//print_r($baidu_coordinate);
	//exit;
	//$baidu_coordinate=getAll_baidu_coordinate();
	$smarty->assign('baidu_coordinate', json_encode($baidu_coordinate));
	
	$smarty->assign('jietu',get_jietulist());
	
	$smarty->display('index.html');
}
/*------------------------------------------------------ */
//-- 顶部框架的内容
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'top')
{
	
	$smarty->display('top.htm');
}

/*------------------------------------------------------ */
//-- 左边的框架
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'menu')
{
	
	//include_once('includes/inc_menu.php');
	$smarty->display('menu.htm');
}
/*------------------------------------------------------ */
//-- 主窗口，起始页
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'main')
{
	
	$smarty->display('main.htm');
}

/**
 * +----------------------------------------------------------
 * 截图保存
 * +----------------------------------------------------------
 */
 elseif ($_REQUEST['act'] == 'jietu')
{
	
	define('UPLOAD_DIR', 'images/jietu/');  
    $img = $_POST['picdata'];  
    $filename=trim($_POST['filename']);

    $img = str_replace('data:image/png;base64,', '', $img);  
    $img = str_replace(' ', '+', $img);  
    $data = base64_decode($img);  
   // if($filename){
  //  	$file = UPLOAD_DIR .$filename. uniqid() . '.png';
  //  }else{
    	
    	$file = UPLOAD_DIR . uniqid() . '.png';
    //}

    $success = file_put_contents($file, $data);  
  //  print $success ? $file : 'Unable to save the file.';  
    if($success){
    
    	//保存到数据库
    	$files['name']=$filename;
    	$files['user_id']=$_USER['user_id'];
    	$files['image']=$file;
    	$files['datetime']=time();
    	
    	$GLOBALS['dou']->autoExecute('m_jietu', $files,"INSERT");
    	
    	$obj['code']=0;
    	$obj['info']=$_CFG['root_url'].$file;
    	
    	
    }else{
    
    	$obj['code']=-1;
    }
    echo json_encode($obj);
    
}
/**查询坐标集合 按城市区域**/

elseif ($_REQUEST['act'] == 'get_label')
{

	$region_id=$_REQUEST['did'];
	
	$json=array('msg'=>'0','data'=>'error','money_heji'=>0,'store_count'=>0);
	
	if($region_id>0){
		
	
		
		$sql="select region_type from m_region where  region_id=$region_id";
		$region_type=$GLOBALS['dou']->get_one($sql);  //位置等级 是省  还是市  还是区 等
		
		if($region_type==3){
			//区
			$where="district=$region_id";
			
		}elseif ($region_type==2){
			//city  市
			$where="city=$region_id";
			
		}elseif ($region_type==1){
			//province  //省
			$where="province=$region_id";
		}else{
			
			$where="province=$region_id";
		}
		$where.=" AND baidu_coordinate!='' ";
		$sql="select *  from m_store where ".$where;  //查询百度坐标
		
		 /////////////////按一级组织分不同颜色显示///////////////////
		// 查询组织1等级的编号
		$lsql="SELECT level1_code , level1_name FROM `m_store`  WHERE level1_code!=''  GROUP BY level1_code";
		
		$level1=$GLOBALS['dou']->getAll($lsql);
		
		//print_r($level1);
		
		foreach ($level1 as $key=>$val){
		
			$level1_code=$val['level1_code'];
			$result[]=$GLOBALS['dou']->getAll($sql." AND  level1_code ='$level1_code' ");
			
		}
		

		 //////////////////////////////////////////////////////////////
		 	//echo $sql;
		//exit;
		$res=$GLOBALS['dou']->getAll($sql);
		//查询当前下门店数量
		//销售额合计
			
		foreach ($res as $k=>$v){
			$money_heji +=$v['api_total_money'];
			$count[]=$v['store_id'];
			$res[$k]['color']=$GLOBALS['dou']->get_one("select color from " .$dou->table('zuzhi')." where code='{$v['level1_code']}'");
				
		}
		//echo $money_heji;
		$json['money_heji']=$money_heji?$money_heji:0;  //销售额合计
		$json['store_count']=count($count);  //门店数量
		$json['msg']=1;
		$json['data']=$res;
		$json['result']=json_encode($result);
		
	}
	echo json_encode($json);
	
	
}
elseif ($_REQUEST['act'] == 'zuzhi_lable')
{
	$json=array('msg'=>'0','data'=>'error','money_heji'=>0,'store_count'=>0);
	
	$code1=trim($_REQUEST['code1']);  // 1等级编号
	$code2=trim($_REQUEST['code2']);  // 2等级编号
	$code3=trim($_REQUEST['code3']);  // 3等级编号
	$code4=trim($_REQUEST['code4']);  // 4等级编号
	$code5=trim($_REQUEST['code5']);  // 5等级编号
	
	$leid=intval($_REQUEST['did']);   //等级 12345
	
	$where .=" where level1_code = '$code1'  ";
	
	if($leid==5){
		$where .="and level5_code ='$code5' and level4_code ='$code4' and level3_code='$code3' and level2_code='$code2'";
	}elseif ($leid==4){
	
		$where.="and level4_code ='$code4' and level3_code='$code3' and level2_code='$code2'";
		
	}elseif ($leid==3){
		
		$where.=" and level3_code ='$code3' and level2_code='$code2' ";
	}elseif ($leid==2){
		
		$where.=" and  level2_code ='$code2' ";
	}elseif ($leid==1){
		
		$where =$where;
	}else{
		
		$where =$where;
	}
	$where.=" AND baidu_coordinate!='' ";
	
	$fenye=$_REQUEST['fenye'];
	
	if(!empty($fenye)){
		$page = $_REQUEST['page']? intval($_REQUEST['page']) : 1;
		$page_url = 'index.php';
		$limit = $GLOBALS['dou']->pager('store', 15, $page, $page_url, $where, $get);
	}else{
		
		$limit='';
	}
	
	
	$sql="select * from m_store  $where order by id desc $limit";
	//echo $sql;
	$res=$GLOBALS['dou']->getAll($sql);
	if($res){
		$json['msg']=1;
		$json['data']=$res;
		
		foreach ($res as $k=>$v){
			$money_heji +=$v['api_total_money'];
			$count[]=$v['store_id'];
		
		}
		
	}

	//echo $money_heji;
	$json['money_heji']=$money_heji?$money_heji:0;  //销售额合计
	$json['store_count']=count($count);  //门店数量
	//如果是分页查询的话   分页总页码数
	if(!empty($fenye)){
		
		$json['record_count']=$_SESSION['page']['record_count'];  //总数
		$json['page_count']  =$_SESSION['page']['page_count'];  //多少页
		
	}
	
	echo json_encode($json);
}
/**
 * 根据id查询店铺详细信息
 */
elseif ($_REQUEST['act'] == 'id_store'){
	
	$id=intval($_REQUEST['id']);
	$sql = "SELECT * FROM " . $dou->table('store') . " where id=$id" ;
	$res=$dou->getRow($sql);
	echo json_encode($res);
	exit;
}
/**
 * 搜索
 */
elseif ($_REQUEST['act'] == 'search_store'){
	 //关键词
	$s=trim($_REQUEST['s']);
	
	// 分页
	/*
	$page = $_REQUEST['page']? intval($_REQUEST['page']) : 1;
	$page_url = 'index.php';
	*/
	$where=" where name like'%$s%' OR phone like'%$s%' OR store_id like'%$s%' ";
	
	//$limit = $dou->pager('store', 15, $page, $page_url, $where, $get);

	$limit='';
	
	$sql = "SELECT * FROM " . $dou->table('store') .  $where ." ORDER BY id DESC  $limit" ;
	//echo $sql;
	$res=$dou->getAll($sql);
	
	$count=count($res);
	
	$data['data']=$res;
	$data['count']=$count;
	//print_r($res);
	//exit;
	echo json_encode($data);
	exit;
}
/**
 *截图搜索 
 */
if($_REQUEST['act']=='jietu_search'){
	
	$name=trim($_REQUEST['name']);
	$start_time=strtotime(trim($_REQUEST['start_time'])) ;
	$end_time=strtotime(trim($_REQUEST['end_time']));
	$user_name=trim($_REQUEST['user_name']);
	

	$where=' ';
	
	if($name){
		
		$where="AND name like '%$name%' ";
	}
	if($start_time && $end_time){
	 $where.="AND datetime>=$start_time and  datetime <=$end_time ";	
	}
	if($user_name){
		
		$uid=$GLOBALS['dou']->get_one("select user_id from m_admin where user_name='$user_name'");
		$where .=" AND  user_id=$uid ";
	}
	
	$sql="select * from m_jietu where  1=1 $where  order by id desc ";
	//echo $sql;
	$res=$GLOBALS['dou']->getAll($sql);
	foreach ($res as $k=>$v){
		
		$res[$k]['user_name']=$GLOBALS['dou']->get_one("select user_name from m_admin where user_id={$v['user_id']}");
		$res[$k]['time']=date("Y-m-d H:i:s",$v['datetime']);
		
	}
	
	echo json_encode($res);
	
}


/**
 * AJAX 返回二维码
 */
elseif ($_REQUEST['act'] == 'qrcode'){

	//$data=$_REQUEST['data'];
	 $data=include_once 'qrcode.php';
      echo $data;
	//echo json_encode($res);
}

/**
 * AJAX 门店分页
 */
elseif ($_REQUEST['act'] == 'store_page'){

	 $res=get_Allstore();


	echo json_encode($res['data']);
}

function get_Allstore($all=''){
	
	$dou=$GLOBALS['dou'];
	// 分页
	$page = $_REQUEST['page']? intval($_REQUEST['page']) : 1;
	$page_url = 'index.php';
	$where='';
	if(empty($all)){
	$limit = $dou->pager('store', 15, $page, $page_url, $where, $get);		
	}
	$sql = "SELECT * FROM " . $dou->table('store') . $where . " ORDER BY id DESC" . $limit;
	$res = $dou->getAll($sql);
	
	
	foreach ($res as $k=>$v){
		$money_heji +=$v['api_total_money'];
		$count[]=$v['store_id'];
		$res[$k]['color']=$GLOBALS['dou']->get_one("select color from " .$dou->table('zuzhi')." where code='{$v['level1_code']}'");
	}
	$json['data']=$res;
	$json['money_heji']=$money_heji?$money_heji:0;  //销售额合计
	//print_r($json);
	return  $json;
}
/**
 * 所有的坐标合计
 */
function  getAll_baidu_coordinate($level1_code=''){
	$dou=$GLOBALS['dou'];
	if(!empty($level1_code)){
		
	$where =" where level1_code='$level1_code' ";
		
	}else{
		$where='';
	}
	$sql = "SELECT store_id,baidu_coordinate,name,phone,address,level1_name,level2_name,level3_name,api_start_day,api_end_day,api_total_num,api_total_money,image FROM " . $dou->table('store') . " $where  ORDER BY id DESC" ;

	$res = $dou->getAll($sql);
	//return  json_encode($res);
	return  $res;
	
}
/**
 * 查询组织信息
 */
function get_zuzhi(){
	//显示组织列表
	
    //首先查询一级代理
	 //一等级的
	$sql="SELECT level1_code , level1_name FROM `m_store` WHERE level1_code!='' GROUP BY level1_code";
	$res=$GLOBALS['dou']->getAll($sql);
	//echo "<pre>";
	 foreach ($res as $k=>$v){
	 	  //二等级的
	 	$sql="select level2_code , level2_name FROM `m_store`    where level1_code ='{$v['level1_code']}' AND level2_code!='' GROUP BY level2_code";
	 	//echo $sql;
	 	$res1=$GLOBALS['dou']->getAll($sql);
	 	
	 	
	 	
	 	foreach ($res1 as $key=>$val){
	 		 // 三等级的
	 		 $sql="select level3_code , level3_name FROM `m_store`   where level2_code ='{$val['level2_code']}' GROUP BY level3_code";
	 		 $res2=$GLOBALS['dou']->getAll($sql);
	 		 
	 		 
	 		 foreach ($res2 as $k2=>$v2){
	 		 	//四等级的
	 		 	$sql="select level4_code , level4_name FROM `m_store`   where level3_code ='{$v2['level3_code']}' GROUP BY level4_code";
	 		 	$res3=$GLOBALS['dou']->getAll($sql);
				
	 		 	foreach ($res3 as $k3=>$v3){
	 		 		
	 		 		//五等级的
	 		 		$sql="select level5_code , level5_name FROM `m_store`   where level4_code ='{$v3['level4_code']}' GROUP BY level5_code";
	 		 		$res4=$GLOBALS['dou']->getAll($sql);
	 		 		
	 		 		//最终六等级的
	 		 	/*	foreach ($res4 as $k4=>$v4){
	 		 			
	 			$sql="select id,store_id,name,address,baidu_coordinate,api_total_money FROM `m_store`   where level5_code ={$v4['level5_code']} ";
	 			$res5=$GLOBALS['dou']->getAll($sql);
	 			$res4[$k4]['final']=$res5;
	 		 		}
	 		 		*/
	 		 		$res3[$k3]['level5']=$res4;
	 		 	}
	 		 	
	 		 	$res2[$k2]['level4']=$res3;
	 		 }
	 		 $res1[$key]['level3']=$res2;
	 	}
	 	

	 	
	 	$res[$k]['level2']=$res1;
	 }
	 //print_r($res);
	 
	return  $res;
	
}


/**
 * 获得指定国家的所有省份
 *
 * @access      public
 * @param       int     country    国家的编号
 * @return      array
 */
function get_regions($type = 0, $parent = 0)
{
    $sql = "SELECT region_id, region_name FROM ".$GLOBALS['dou']->table('region')."  WHERE region_type = '$type' AND parent_id = '$parent'";
	//echo $sql;	
    return $GLOBALS['dou']->getAll($sql);
}
/**
 * 
 * 查询截图列表
 * @return unknown
 */
function  get_jietulist(){
	
	$sql="select * from m_jietu order by id desc";
	$res= $GLOBALS['dou']->getAll($sql);
	
	foreach ($res as $k=>$v){
		
		$res[$k]['user_name']=$GLOBALS['dou']->get_one("select user_name from m_admin where user_id={$v['user_id']}");
		$res[$k]['time']=date("Y-m-d H:i:s",$v['datetime']);
	}
	return $res;
}
?>