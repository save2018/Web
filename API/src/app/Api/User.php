<?php
namespace App\Api;

use PhalApi\Api;

//use App\Model\User as ModelUser;
use App\Domain\User as DomainUser;
use App\Domain\Store as DomainStore;
use App\Domain\Region as DomainRegion;


use App\Common\Smsserver as SMS;
/**
 * 用户模块接口服务
 */
class User extends Api {
    public function getRules() {
        return array(
            'login' => array(
                'user_id' => array('name' => 'user_id', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '用户ID'),
              //  'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '用户名'),
                'openid' => array('name' => 'openid', 'require' => false,'desc' => '微信openid'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
                'nickName' => array('name' => 'nickName', 'require' => false,),
                'avatarUrl' => array('name' => 'avatarUrl', 'require' => false,),
            ),
            'info' =>array(
                'uid' => array('name' => 'uid', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'Token'),

            ),
            'store_count'=>array(
                'uid' => array('name' => 'uid', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'ID'),
                'wxid' => array('name' => 'wxid', 'require' => false, 'min' => 1, 'max' => 50, 'desc' => 'openid'),
                'sid'      => array('name' => 'sid', 'require' => false,),

            ),
            'up_level'=>array(

           //     'uid' => array('name' => 'uid', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'Token'),
                'code_up'=>array('name' => 'code_up', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '上级代理编号'),
            ),
            'getOpenid'=>array(

                'code' => array('name' => 'code', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'code'),

            ),
            'sendsms'=>array(

                'mobile' => array('name' => 'mobile', 'require' => true,),
            ),
            'sms_verif'=>array(

                'phone' => array('name' => 'phone', 'require' => true,),
                'smscode' => array('name' => 'smscode', 'require' => true,),
            ),
            'saixuan'=>array(

                'uid' => array('name' => 'uid', 'require' => true,),

                 'wxid' => array('name' => 'wxid', 'require' => true,),
            ) ,
        	'isdown'=>array(
        		
        				'uid' => array('name' => 'uid', 'require' => true,),
        	) ,


        );
    }

    /**用户信息
     * @return array
     */
    public  function  info(){

        $uid = $this->uid;   //
        $token = $this->token;   //
        $domain=new DomainUser();

        $res=$domain->get_info($uid,$token);


        return array('is_login' => 1, 'user_id' => $uid);
    }
    /**
     * 查询权限
     * @return multitype:number unknown
     */
    public  function  isdown(){
    
    	$uid = $this->uid;   //
    	$domain=new DomainUser();
    
    	$res=$domain->get($uid,'is_down');
    
    
    	return array('isdown' => $res['is_down']);
    }

    /** 查询店铺总数
     * @return array
     */
    public  function  store_count(){
        //$token = $this->token;   //

        $uid=$this->uid;

        $wxid=$this->wxid;

        $sid=$this->sid ? $this->sid:0;

        $doman_u=new DomainUser();

        //当前用户等级下  所有的uid
        $us=$doman_u->get_usid($uid,$wxid,$sid);


        $domain=new DomainStore();
        $res=$domain->get_store_count($us);
        return $res;
    }

    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     * @return boolean is_login 是否登录成功
     * @return int user_id 用户ID
     */
    public function login() {

        $password = trim($this->password);   // 密码参数
        $user_id = trim($this->user_id);   // 账号参数
        $openid=$this->openid;  //微信openid

        $nickName = @preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $this->nickName);
        
        $WDATA['nickName'] =$nickName;
        $WDATA['avatarUrl']=$this->avatarUrl;
        $WDATA['openid']   =$openid;
        // 更多其他操作……
        $domain=new DomainUser();

        //查询密码

        $res=$domain->get_user($user_id,$password,$openid);
        $uid=$res['id'];

        if($res){
            //登录成功 将登录后的数据存入session
            $token=md5(microtime());
            \PhalApi\DI()->cache->set($uid.'token',$token,24*60*60*7);//15天 1296000
            \PhalApi\DI()->cache->set('user_id',$user_id, 24*60*60*7);//15天
            \PhalApi\DI()->cache->set('uid',$uid, 24*60*60*7);//15天

            //保存登录信息

            //保存微信信息
            $domain->insert_wechat($WDATA);

            return array('is_login' => true,'user'=>$res,'token'=>$token, 'user_id' => $user_id,'uid'=>$res['id']);
        }else{


            return array('is_login' => false, 'user_id' => '');
        }



    }

    /**
     * 查询地址信息
     */
    public  function region_list(){
        $domain=new DomainRegion();
        $res=$domain->get_region();
        return  $res;
    }


    /**
     * 上级代理查询
     */
    public  function up_level(){

        $domain= new DomainUser();
        //查询上级代理; 三级或四级
        //$uid=$this->uid;
         $code_up=$this->code_up;
       $res= $domain->up_level($code_up);

       return $res;
    }


    // 获取openid
    public function getOpenid(){ // $code为小程序提供

        $code=$this->code;
        $appid = 'wx31dd3e3bd2a9ed73'; // 小程序APPID
        $secret = '96f8be5da518cbffc2888627090468a4'; // 小程序secret
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);

        return json_decode($res, true); // 这里是获取到的信息
    }

    /**
     * 发送验证码
     */
    public  function  sendsms(){

         //require_once '../Common/SMS_ServerAPI.php';



        $mobile=$this->mobile;

        $p=new SMS('a2c90fcbb71cfe85ac1825fe785889ed','7034426c5fe7');
            //$p=new SMS('84cb214d12ffd14256668ada32f354cb','3a481bcc79ba');//ServerAPI();
            //发送短信验证码  9484146：模板id  ；5:验证码长度
            $send=$p->sendSmsCode('9414534',$mobile,'','5');
            //print_r($send);
            if($send['code']=='200'){


                \PhalApi\DI()->cache->set($mobile.'sms_code',$send['obj'], 60*60*24);//24小时

                //$_SESSION['sms_code']=$send['obj'];  //验证码
                //$_SESSION['sms_time']=time();        //时间
                $data=array("code"=>1,'msg'=>'ok');
            }else{

                $data=array("code"=>2,'msg'=>'发送失败，请重试');
            }


        return $data;

    }

    /**
     * 验证码验证
     */
    public  function  sms_verif(){

        $phone=$this->phone;
        $smscode=$this->smscode;
        $code=\PhalApi\DI()->cache->get($phone.'sms_code');
        if($smscode==$code){

                return true;
        }else{

            return false;
        }

    }



    //筛选用户等级名称等

    public  function   saixuan(){

        $uid=$this->uid;
        $wxid=$this->wxid;
        $domain=new DomainUser();

        //$us=$domain->get_usid($uid,$wxid);

        $saixuan=$domain->get_us($uid);

        return $saixuan;
    }




} 
