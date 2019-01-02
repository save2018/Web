<?php

set_time_limit(0);  
/* 
include_once(dirname(__FILE__).'/admin/conn.php');
 $db =  new DB();
*/
define('IN_DOUCO', true);

require (dirname(__FILE__) . '/include/init.php');
 //通过地址 提取出省份 城市 和 地区
 //echo "123";
// exit;

 
 get_baidu_zuobiao();
 getbaidu_();
  
 //echo "<script>location.href='store.php';</script>"; 
  
// update_city();
//update_district();
// 首先省份名称 更新省份  

function  update_province(){
	
	 $db =  $GLOBALS['dou'];
	$sql="select region_id,region_name from m_region where region_type=1";

$res=$db->getAll($sql);

foreach($res as $k=>$v){
	
	
	$sql="UPDATE m_store  SET province =".$v['region_id']." WHERE province=0 AND address LIKE '%".$v['region_name']."%'";
	$db->query($sql);

}
	echo "省份更新完成";
}

//更新城市id

function  update_city(){
	 $db =  $GLOBALS['dou'];
	 //查询出所有的城市
	$sql="select region_id,parent_id,region_name from m_region where region_type=2";

	$res=$db->getAll($sql);

	foreach($res as $k=>$v){
		
		//查询city=0的 并且 省份也一样的  更新城市id
		
		$sql="UPDATE m_store  SET city =".$v['region_id']." WHERE city=0 AND province=".$v['parent_id']." AND address LIKE '%".$v['region_name']."%'";
		$db->query($sql);

	}
	
	
	  echo "城市名更新完成";
}


//更新地区ID

function  update_district(){
	 $db =  $GLOBALS['dou'];
	 //查询出所有的地区
	$sql="select region_id,parent_id,region_name from m_region where region_type=3";

	$res=$db->getAll($sql);

	foreach($res as $k=>$v){
		
		//查询 并且 省份也一样的  更新城市id
		
		$sql="UPDATE m_store  SET district =".$v['region_id']." WHERE city=".$v['parent_id']."  AND address LIKE '%".$v['region_name']."%'";
		$db->query($sql);

	}
	
	
	  echo "地区更新完成";
}


//批量查询 高德坐标

function get_baidu_zuobiao(){
		$db = $GLOBALS['dou']; //new DB();
	 	
		$sql="select id,address from m_store where gaode_coordinate =''";   //baidu_coordinate
		$res=$db->getAll($sql);
		//print_r($res);
		$gaode="https://restapi.amap.com/v3/place/text?s=rsv3&children=&key=8325164e247e15eea68b59e89200988b&page=1&offset=10&city=310000&language=zh_cn&callback=jsonp_102442_&platform=JS&logversion=2.0&sdkversion=1.3&appname=https%3A%2F%2Flbs.amap.com%2Fconsole%2Fshow%2Fpicker&csid=8392EB07-A84F-4B92-A562-BC2B2B70A3D2&keywords=";
		
		foreach($res as $k=>$v){
			
			$id=$v['id'];
			$address=trim($v['address']);
			//$json=file_get_contents($gaode.$address);
			//echo $json;
			
			$json=@GoCurl($gaode, $address, 'GET');
			//echo $json;exit;
			$cont=explode(':',$json);
			$zuoarr=explode('"',$cont[15]);
			if($zuoarr[1]){
				
			$zuobiao=$zuoarr[1];
			$sql="update m_store set gaode_coordinate='$zuobiao' where id={$v['id']} ";
			$db->query($sql);
			echo $id.$address."成功<br> ";
			}else{
				
				
				echo $id.$address."失败<br> ";
			}


			
			//$json=curl_get($gaode.$v['address']);
		//	$json = htmlspecialchars_decode($json);
	//	$info = json_decode($json,true);
	//echo $errorinfo = json_last_error();   //输出4 语法错误
		
		
			
		}
		echo "高德坐标更新完成";
	
}
function getbaidu_(){
	
		$db =  $GLOBALS['dou'];//new DB();
	 	
		$sql="select id , gaode_coordinate from m_store where baidu_coordinate =''"; 
		
		$res=$db->getAll($sql);
		
		foreach($res as $k=>$v){
			
			if(!empty($v['gaode_coordinate'])){
				
				$xy=explode(",",$v['gaode_coordinate']);
				//print_r($xy);
				$x=$xy[0];
				$y=$xy[1];
				$ty=bd_encrypt($x,$y);
				
					if($ty){
						
				    $zuobiao=$ty['bd_lon'].",".$ty['bd_lat'];
					 
					$sql="update m_store set baidu_coordinate='$zuobiao' where id={$v['id']} ";
					$db->query($sql);
					}
			}
			
		}
		echo "百度坐标更新完成";
	
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

	
	    //BD-09(百度)坐标转换成GCJ-02(火星，高德)坐标
    //@param bd_lon 百度经度
    //@param bd_lat 百度纬度
   function bd_decrypt($bd_lon,$bd_lat){
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $bd_lon - 0.0065;
    $y = $bd_lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    // $data['gg_lon'] = $z * cos($theta);
    // $data['gg_lat'] = $z * sin($theta);
    $gg_lon = $z * cos($theta);
        $gg_lat = $z * sin($theta);
        // 保留小数点后六位
        $data['gg_lon'] = round($gg_lon, 6);
        $data['gg_lat'] = round($gg_lat, 6);
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
