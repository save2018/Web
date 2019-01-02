<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

/**

CREATE TABLE `phalapi_curd` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `title` varchar(20) DEFAULT NULL,
    `content` text,
    `state` tinyint(4) DEFAULT NULL,
    `post_date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

 */

class Store extends NotORM {
     /**重新指定表名
    protected function getTableName($id) {
        return 'store';
    }
    */
   public  function  getstore($id){

       $store = $this->getORM();
       // SELECT id, name, age FROM `tbl_user`
       //$store->select('id, name, age')

       $res=$store->where('id', $id)->fetchOne();

        return $res;
   }

    /**查询店铺总数 代理下面的
     * @param $uid
     * @return array
     */
    public  function get_store_count($uid,$show_shenhe){

        if($show_shenhe=='1'){

            $where['auditing_status']='1';
        }
        $where['uid']=$uid;
        $total =$this->getORM()->where($where)->count('id');

        $total1 = $this->getORM()->where('type_id',1)->where($where)->count('id');
        $total2 = $this->getORM()->where('type_id',2)->where($where)->count('id');
        /*
        $total1 = $this->getORM()->where('type_id',1)
            ->where('uid',$uid)
            ->where('auditing_status','1')//通过审核的
            ->count('id'); //维娜店铺
        $total2 = $this->getORM()->where('type_id',2)
            ->where('uid',$uid)
            ->where('auditing_status','1')//通过审核的
            ->count('id');  //SPA店铺
        */
        return array('total'=>$total,'total1'=>$total1,'total2'=>$total2) ;
   }

    /**
     * 查询店铺总数  以wxid 的方式查询单个人的
     */
   public  function  get_store_count_wx($wxid,$did,$show_shenhe){

       if($show_shenhe=='1'){

           $where['auditing_status']='1';
       }
       $where['1']=1;
       $total =$this->getORM()->where('openid',$wxid)->where($where)->count('id');

       $where['openid']=$wxid;
       $where['uid']=$did;
       $total1 = $this->getORM()->where($where)->where('type_id',1)->count('id'); //维娜店铺
       $total2 = $this->getORM()->where($where)->where('type_id',2)->count('id'); //SPA店铺

       // $total1 = $this->getORM()->where(array('openid'=>$wxid,'uid'=>$did,'type_id'=>1))->where('auditing_status','1')->count('id'); //维娜店铺
       //$total2 = $this->getORM()->where(array('openid'=>$wxid,'uid'=>$did,'type_id'=>2))->where('auditing_status','1')->count('id');  //SPA店铺

       return array('total'=>$total,'total1'=>$total1,'total2'=>$total2) ;

   }

    /**查询店铺列表
     * @param $us
     * @param $page
     * @param $perpage
     * @param $keyword
     * @param $show_shenhe  //是否审核后才显示店铺
     * @return mixed
     */
    public function getListItems($us, $page, $perpage,$keyword,$show_shenhe) {

       //print_r($us['uid']);//$us['uid']
        $where['1']=1;
        if($show_shenhe=='1'){

            $where['auditing_status']='1';
        }


       if($us['uid']){

           if($keyword){
                    if(strlen($keyword)==11){

                        $base_phone=base64_encode($keyword);
                    }else{
                        $base_phone=$keyword;
                    }

               return $this->getORM()
                   ->select('*')
                   ->where('uid',$us['uid'])
                  //->where('auditing_status','1') //通过审核的
                   ->where($where) //
                   ->where('store_name like ? or  phone = ?', "%$keyword%", "$base_phone")
                   ->order('id DESC')
                   ->limit(($page - 1) * $perpage, $perpage)
                   ->fetchAll();

           }else{


               return $this->getORM()
                   ->select('*')
                   ->where('uid',$us['uid'])
                   //->where('auditing_status','1') //通过审核的
                   ->where($where)
                   ->order('id DESC')
                   ->limit(($page - 1) * $perpage, $perpage)
                   ->fetchAll();

           }



       }else{


           if($keyword){

               if(strlen($keyword)==11){

                   $base_phone=base64_encode($keyword);
               }else{
                   $base_phone=$keyword;
               }

               return $this->getORM()
                   ->select('*')
                   ->where('openid',$us['wxid'])
                   ->where('uid',$us['did']?$us['did']:$us['uid'])
                   //->where('auditing_status','1') //通过审核的
                   ->where($where)
                   ->where('store_name like ? or  phone = ?', "%$keyword%", "$base_phone")
                   ->order('id DESC')
                   ->limit(($page - 1) * $perpage, $perpage)
                   ->fetchAll();
           }else{

               return $this->getORM()
                   ->select('*')
                   ->where('openid',$us['wxid'])
                   //->where('auditing_status','1') //通过审核的
                   ->where($where)
                   ->where('uid',$us['did']?$us['did']:$us['uid'])
                   ->order('id DESC')
                   ->limit(($page - 1) * $perpage, $perpage)
                   ->fetchAll();

           }


       }


    }

    public function getListTotal($us,$keyword,$show_shenhe) {

        $where['1']=1;
        if($show_shenhe=='1'){

            $where['auditing_status']='1';
        }


        if($us['uid']){


            if($keyword){

                if(strlen($keyword)==11){

                    $base_phone=base64_encode($keyword);
                }else{
                    $base_phone=$keyword;
                }

                $total = $this->getORM()
                    ->where('uid', $us['uid'])
                  //  ->where('auditing_status','1') //通过审核的
                    ->where($where)
                    ->where('store_name like ? or  phone = ?', "%$keyword%", "$base_phone")
                    ->count('id');

            }else{


                $total = $this->getORM()
                    ->where('uid', $us['uid'])
                   // ->where('auditing_status','1') //通过审核的
                   ->where($where)
                    ->count('id');
            }


        }else{
            if($keyword){
                if(strlen($keyword)==11){

                    $base_phone=base64_encode($keyword);
                }else{
                    $base_phone=$keyword;
                }

                $total = $this->getORM()
                    ->where('openid', $us['wxid'])
                   // ->where('auditing_status','1') //通过审核的
                   ->where($where)
                    ->where('uid',$us['did']?$us['did']:$us['uid'])
                    ->where('store_name like ? or  phone = ?', "%$keyword%", "$base_phone")
                    ->count('id');

            }else{

                $total = $this->getORM()
                    ->where('openid', $us['wxid'])
                   // ->where('auditing_status','1') //通过审核的
                    ->where($where)
                    ->where('uid',$us['did']?$us['did']:$us['uid'])
                    ->count('id');
            }


        }




        return intval($total);
    }

    /**通过uid查询所有店铺
     * @param $uid
     */
    public  function  get_xyall($uid,$show_shenhe){

        $where['1']=1;
        if($show_shenhe=='1'){

            $where['auditing_status']='1';
        }


        return $this->getORM()
            ->select('store_name,province,city,district,address,xy_axis')
            ->where('uid', $uid)
            //->where('auditing_status','1') //通过审核的
            ->where($where)
            ->order('id DESC')
            ->fetchAll();
    }

    /** 通过微信id查询所有店铺
     * @param $wxid
     * $did 用户uid
     */
    public  function  get_xy_wx($wxid,$did,$show_shenhe){
        $where['1']=1;
        if($show_shenhe=='1'){

            $where['auditing_status']='1';
        }

        return $this->getORM()
            ->select('store_name,province,city,district,address,xy_axis')
            ->where('openid', $wxid)
            //->where('auditing_status','1') //通过审核的
            ->where('uid', $did)
            ->where($where)
            ->order('id DESC')
            ->fetchAll();
    }

    /**根据城市 或者 省份 查询店铺坐标
     * @param $city
     * @param $province
     * @return mixed
     */
    public  function  strict_store($city,$province){
        if(!empty($city)){
            $res=$this->getORM()
                ->select('id,type_id,xy_axis')
                ->where('city',$city)
                ->fetchAll();
        }else{

            $res=$this->getORM()
                ->select('id,type_id,xy_axis')
                ->where('province',$province)
                ->fetchAll();

        }
       return $res;

    }
}
