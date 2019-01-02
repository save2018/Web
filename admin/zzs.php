<?php
 
//include_once('conn.php');
//$db =  new DB();
define('IN_DOUCO', true);

require (dirname(__FILE__) . '/include/init.php');

set_time_limit(0);
echo "查询中请稍等";

$db=$GLOBALS['dou'];
//查询1等级的转化数据

$sql="SELECT level1_code,level1_name FROM  m_store where level1_code!='' GROUP BY  level1_code";

$res=$db->getAll($sql);

//print_r($res);

foreach($res as $k=>$v){
	
	$data['code']=$v['level1_code'];
	$data['parent_id']=0;
	$data['zname']=$v['level1_name'];
	$data['cid']=1;
	//添加到数据库
		//添加到数据库
	$id=$db->get_one("select zid from m_zuzhi where code='{$data['code']}'");
	if(empty($id)){

			$db->autoExecute('m_zuzhi',$data,"INSERT");
	}
	
}



//查询2 3 4 5等级的转化数据


//2
$sql="SELECT level1_code,level1_name,level2_code , level2_name FROM `m_store` WHERE level2_code!='' GROUP BY level2_code";

$res=$db->getAll($sql);

//print_r($res);
unset($data);
foreach($res as $k=>$v){
	
	
	$data['code']    =$v['level2_code'];
	$parent_code    =  $v['level1_code'];
	$data['parent_id']=$db->get_one("select zid from m_zuzhi where code='$parent_code'");
	$data['zname']=$v['level2_name'];
	$data['cid']=2;
	//添加到数据库
	$id=$db->get_one("select zid from m_zuzhi where code='{$data['code']}'");
	if(empty($id)){

			$db->autoExecute('m_zuzhi',$data,"INSERT");
	}

	//unset($data);
}




//3
$sql="SELECT level2_code,level2_name,level3_code , level3_name FROM `m_store` WHERE level3_code!='' GROUP BY level3_code";

$res=$db->getAll($sql);

//print_r($res);
unset($data);
foreach($res as $k=>$v){
	
	
	$data['code']    =$v['level3_code'];
	$parent_code    =  $v['level2_code'];
	$data['parent_id']=$db->get_one("select zid from m_zuzhi where code='$parent_code'");
	$data['zname']=$v['level3_name'];
	$data['cid']=3;
	//添加到数据库
	$id=$db->get_one("select zid from m_zuzhi where code='{$data['code']}'");
	if(empty($id)){

			$db->autoExecute('m_zuzhi',$data,"INSERT");
	}

	//unset($data);
}


//4
$sql="SELECT level3_code,level3_name,level4_code , level4_name FROM `m_store` WHERE level4_code!='' GROUP BY level4_code";

$res=$db->getAll($sql);

//print_r($res);
unset($data);
foreach($res as $k=>$v){
	
	
	$data['code']    =$v['level4_code'];
	$parent_code    =  $v['level3_code'];
	$data['parent_id']=$db->get_one("select zid from m_zuzhi where code='$parent_code'");
	$data['zname']=$v['level4_name'];
	$data['cid']=4;
	//添加到数据库
	$id=$db->get_one("select zid from m_zuzhi where code='{$data['code']}'");
	if(empty($id)){

			$db->autoExecute('m_zuzhi',$data,"INSERT");
	}

	//unset($data);
}



  //5

$sql="SELECT level4_code,level4_name,level5_code , level5_name FROM `m_store` WHERE level5_code!='' GROUP BY level5_code";
unset($data);
$res=$db->getAll($sql);

//print_r($res);

foreach($res as $k=>$v){
	
	
	$data['code']    =$v['level5_code'];
	$parent_code    =  $v['level4_code'];
	$data['parent_id']=$db->get_one("select zid from m_zuzhi where code='$parent_code'");
	$data['zname']=$v['level5_name'];
	$data['cid']=5;
	//添加到数据库
	$id=$db->get_one("select zid from m_zuzhi where code='{$data['code']}'");
	if(empty($id)){

			$db->autoExecute('m_zuzhi',$data,"INSERT");
	}
	//unset($data);
}



 echo "<script>location.href='zuzhi.php';</script>"; 







