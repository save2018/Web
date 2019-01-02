<?php

namespace App\Api;

use App\Model\Config;
use PhalApi\Api;

use App\Model\Store as ModelStore;
use App\Domain\Store  as DomainStore;
use App\Domain\User  as DomainUser;
use App\Model\Config  as ModelConfig;
use App\Domain\Region as DomainRegion;
/**
 * Class Store
 * @package App\Api
 * 店铺信息接口
 */

class Store extends Api{


    public function getRules() {
        return array(
            'store' => array(
                'id' => array('name' => 'id', 'require' => true,),
            ),
            'store_add'=>array(

                'uid' => array('name' => 'uid', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'Token'),
                'name' => array('name' => 'name', 'require' => true,),
                'phone' => array('name' => 'phone', 'require' => true,),
                'store_name' => array('name' => 'store_name', 'require' => true,),
                'store_type' => array('name' => 'store_type', 'require' => true,),
                'xy_axis' => array('name' => 'xy_axis', 'require' => true,),
                'district'  => array('name' => 'district', 'require' => true,),
                'province' => array('name' => 'province', 'require' => true,),
                'city'     => array('name' => 'city', 'require' => true,),
                'address'  => array('name' => 'address', 'require' => true,),
                'openid'   =>array('name' => 'openid', 'require' => false,),
                'image_url'=>array('name' => 'image_url', 'require' => false,),
            ),
            'upload_img' => array(
                'file' => array(
                    'name' => 'file',        // 客户端上传的文件字段
                    'type' => 'file',
                    'require' => true,
                    'max' => 2097152,        // 最大允许上传2M = 2 * 1024 * 1024,
                    'range' => array('image/jpeg', 'image/png','image/jpg'),  // 允许的文件格式
                    'ext' => 'jpeg,jpg,png', // 允许的文件扩展名
                    'desc' => '待上传的图片文件',
                ),
                'uid' => array('name' => 'uid', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => 'Token'),
                'id'=>array('name' => 'id', 'require' => true,),

            ),
            'store_list' => array(
                'page'      =>array('name' => 'page', 'default'=>1),
                'perpage'  =>array('name' => 'perpage', 'require' => true,),
                'username' => array('name' => 'username', 'require' => false,),
                'phone'    => array('name' => 'phone', 'require' => false,),
                'uid'      => array('name' => 'uid', 'require' => true,),
                'wxid'      => array('name' => 'wxid', 'require' => false,),
                'keyword'   =>array('name'=>'keyword','require' => false,),
                'sid'      => array('name' => 'sid', 'require' => false,),

            ),

            'xy_all' => array(
                'uid'      => array('name' => 'uid', 'require' => true,),
                'wxid'     => array('name' => 'wxid', 'require' => true,),
                'sid'      => array('name' => 'sid', 'require' => false,),
            ),
            //是否开通位置查询功能
            'config' =>array(

                'uid'      => array('name' => 'uid', 'require' => true,),
            ),
            //通过省市查店铺
            'strict_store' =>array(
                'uid'      => array('name' => 'uid', 'require' => true,),
                'province' => array('name' => 'province', 'require' => false,),//省
                'city'     => array('name' => 'city', 'require' => false,),//市
            ),



        );
    }

    /**
     * 配置信息
     */
    public  function  config(){
        $config_model=new ModelConfig();
        $val=$config_model->open_line();
        return $val;
    }
    /**查询店铺详细信息
     * @return mixed
     */
    public  function  store(){

        $id=$this->id;
        $model= new ModelStore();
        $res=$model->getstore($id);

        return $res;
        //return array('id' => $id, 'store_id' => 'C000087');
    }

    /**新增店铺
     * @return
     */
    public  function  store_add(){

        $data['uid']= $this->uid;
       // $data['code_up']=$this->code_up;
        $data['type_id']=$this->store_type;
        $data['name']   =$this->name;
        $data['phone']=base64_encode($this->phone);
        $data['store_name']=$this->store_name;
        $data['xy_axis']=$this->xy_axis;
        $data['province']=$this->province;
        $data['city']=$this->city;
        $data['district']=$this->district;
        $data['address']=$this->address;
        $data['openid']=$this->openid;
        $data['image_url']=$this->image_url;
        $domain= new DomainStore();
        $res['id']=$domain->insert($data);

        return  $res;

    }

    /**
     * 查询列表
     */
    public  function  store_list(){

        $where='';
        $uid=$this->uid;
        $wxid=$this->wxid;
        $keyword=trim($this->keyword);

        $doman_u=new DomainUser();

        //当前用户等级下  所有的uid

        $sid=$this->sid ? $this->sid:0;

        $us=$doman_u->get_usid($uid,$wxid,$sid);

        //print_r($us);

        $page=$this->page;
        $perpage=$this->perpage;

        $domain= new DomainStore();
        $res=$domain->getList($us,$page,$perpage,$keyword);
       // print_r($res);
        return $res;
       // return array('data' => 'list', 'num' => '20');
    }

    /**
     * 查询条件所有的标注点信息等
     */
    public  function  xy_all(){

        $uid=$this->uid;

        $wxid=$this->wxid;

        $doman_u=new DomainUser();

        $sid=$this->sid ? $this->sid:0;
        //当前用户等级下  所有的uid
        $us=$doman_u->get_usid($uid,$wxid,$sid);

       // print_r($us);
       // exit;

        $domain= new DomainStore();
        //$us=array('uid'=>'','wxid'=>'')
        $res=$domain->get_xyall($us);
       // print_r($res);
        return  $res;

    }

    /**
     * 图片上传
     */
    public  function  upload_img(){
        date_default_timezone_set("Asia/Shanghai"); //设置时区

        $tmpName = $this->file['tmp_name'];

        $id=$this->id;
        $rs=array();

        if(is_uploaded_file($_FILES['file']['tmp_name'])) {


            $name = md5($this->file['name'] . $_SERVER['REQUEST_TIME']);
            $ext = strrchr($this->file['name'], '.');
            $uploadFolder = sprintf('%s/public/uploads/', API_ROOT);

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777);
            }
            //把文件转存到你希望的目录（不要使用copy函数）
            $uploaded_file=$_FILES['file']['tmp_name'];

           /* $username = "min_img";
            //我们给每个用户动态的创建一个文件夹
            $user_path=$_SERVER['DOCUMENT_ROOT']."/m_pro/".$username;
            //判断该用户文件夹是否已经有这个文件夹
            if(!file_exists($user_path)) {
                //mkdir($user_path);
                mkdir($user_path,0777,true);
            }
            */
            //$file_true_name=$_FILES['file']['name'];

            //$move_to_file=$uploadFolder."/".time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,"."));//strrops($file_true,".")查找“.”在字符串中最后一次出现的位置

            $move_to_file = $uploadFolder .  $name . $ext;
            //echo "$uploaded_file   $move_to_file";
            if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {
               // echo $_FILES['file']['name']."--上传成功".date("Y-m-d H:i:sa");
               // echo  sprintf('public/uploads/%s%s', $name, $ext);
                $rs['code'] = 1;
                $rs['url']  = sprintf('public/uploads/%s%s', $name, $ext);

                //图片地址写入数据库

                $domain= new DomainStore();
                $data['image_url']=$rs['url'];
                $domain->update($id,$data);
                //$rs['url'] = sprintf('http://%s/uploads/%s%s', $_SERVER['SERVER_NAME'], $name, $ext);

            } else {
               // echo "上传失败".date("Y-m-d H:i:sa");
                $rs['code']=0;

            }
        } else {
            //echo "上传失败".date("Y-m-d H:i:sa");
            $rs['code']=0;
        }


        return $rs;
    }

    /**
     * 通过省市查店铺
     */
    public  function  strict_store(){

        $uid=$this->uid;
        $province=$this->province;
        $city    =$this->city;
        //查询当前省或市的id
        $domain= new DomainRegion();
        $store_domain=new DomainStore();

        $city_id=$domain->region_id($city);
        //echo $city_id;
        $province_id=$domain->region_id($province);

        $list=$store_domain->strict_store($city_id,$province_id);

        return $list;
        //

    }

}