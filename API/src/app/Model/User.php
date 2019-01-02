<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;



class User extends NotORM {
     /**重新指定表名
    protected function getTableName($id) {
        return 'store';
    }
    */
   public  function  getUser($user_id,$password,$openid){

     //  $return=array();

       $pwd=$this->getORM()->where('user_id',$user_id)->fetchOne('password');


       if(password_verify($password,$pwd)){

           //echo "密码正确";

        $res= $this->getORM()
         ->select('id,user_id,level_code,username,phone,code_up,openid,is_down')
         //->where(array('user_id' => $user_id, 'password' => $password))
        ->where('user_id',$user_id)
        ->fetchOne();

       $res['phone']=base64_decode($res['phone']);

       //上级代理名字
        if($res['code_up']){

            $res['up_name']=$this->getORM()->where('code',$res['code_up'])->fetchOne('username');
        }

        if($res && $res['openid'] ==''){

               $data = array('openid' => $openid, 'update_time' => time());
               $this->getORM()->where('id',$res['id'])->update($data); //基于主键的快速更新
        }

           return  $res;

       }else{

           //echo "密码错误";
           return false;

       }
        /*
       $res= $this->getORM()
               //  ->select('*')
                 ->where(array('user_id' => $user_id, 'password' => $password))
                 ->fetchOne();
       */

        //return $res;
   }

    /**
     * 查询上级代理
     */
    public  function  up_level($code_up){

        //$sql = "SELECT code_up FROM `m_user` WHERE  id=$uid";
       // $rows = $this->getORM()->queryAll($sql);
        $rt=array();
        //$code_up=$this->getORM()->where('id', $uid)->fetchOne('code_up');
        /*if(!$code_up){
            $code_up=$this->getORM()->where('id', $uid)->fetchOne('code_up');
        }
        */
        $res=$this->getORM()->select('id','username','code_up')->where('code',$code_up)->fetchOne();

        $rt[]=$res;
        if($res['code_up']){

            $t1=$this->getORM()->select('id','username','code_up')->where('code',$res['code_up'])->fetchOne();
            $rt[]=$t1;
              if($t1['code_up']) {

                  $t2=$this->getORM()->select('id','username','code_up')->where('code',$t1['code_up'])->fetchOne();
                  $rt[]=$t2;
                  if($t2['code_up']){

                      $t3=$this->getORM()->select('id','username','code_up')->where('code',$t2['code_up'])->fetchOne();
                      $rt[]=$t3;
                      if($t3['code_up']){
                      		
                     	 $t4=$this->getORM()->select('id','username','code_up')->where('code',$t3['code_up'])->fetchOne();
                      	
                      	$rt[]=$t4;
                      }
                  }
              }
        }
        return  array_reverse($rt); //顺序反转，上级从大到小
        //print_r($rt);
        //echo $code_up;0


    }

    /**
     * 当前用户uid  当前用户wxid
     * 查询当前用户下面所有的代理uid
     * 如果是高等级的需要查询下级所有的话 传入sid
     */
    public  function  get_usid($uid,$wxid,$sid){

        $res=array();
        //如果高等级查询下级的话，
        if(isset($sid) && $sid>0){

            $user=$this->getORM()
                ->where(array('id' => $sid,))
                ->fetchOne();
            $uid=$sid;
        }else{
            $user=$this->getORM()
                ->where(array('id' => $uid, ))
                ->fetchOne();

            //当是县代理的时候
            //当不是默认账号绑定的微信id登录的话 只显示当前微信id的店铺信息
            if($user['level_code']=='G'){

                if($wxid!=$user['openid']){

                    $res['uid']='';
                    $res['did']=$uid; //当前用户id
                    $res['wxid']=$wxid;
                    return $res;
                }
            }

            /*
            if(!$user){

                $res['uid']='';
                $res['wxid']=$wxid;
                return $res;
            }
            */


        }
       // echo  $sid;

            $newid=array();
            //如果是绑定的微信登录的话先判断是哪个代理
            //常务J-理事L-省代D-市代B-县代G
                switch($user['level_code']){

                    //县代理
                    case 'G':
                    $res['uid']=$uid;

                    //return $res;

                    break;
                    //市代理
                    case  'B':
                        //$sql="select id from m_user where  code_up='{$user['code']}'"  ;
                           $late=$this->late($user['code']);
                                             	
                           	foreach ($late as $k=>$v){
                           	
                           		$newid[]=$v['id'];
                           	}
                           	$res['uid']=!empty($newid)? $newid:$uid;



                       //return   $res;
                    break;
                    //省代理
                    case   'D':
                        //查询市代理再查询县代理
                        $code=array();
                        $late=$this->late($user['code']);
                        foreach ($late as $m=>$n){
                            $code[]=$n['code'];
                        }
                        $late1=$this->late($code);
                        foreach ($late1 as $k=>$v){

                            $newid[]=$v['id'];
                        }
                        $res['uid']=!empty($newid)? $newid:$uid;
                       // return   $res;



                        break;
                     //理事
                    case 'L':
                        //查询省 ->查询市->查询县
                        $late=$this->late($user['code']); //省代code
                        foreach ($late as $k=>$v){
                            $code[]=$v['code'];
                        }
                        $late1=$this->late($code); //市代
                        foreach ($late1 as $t=>$m){
                            $code1[]=$m['code'];
                        }
                        $late2=$this->late($code1); //县代
                        foreach ($late2 as $p=>$q){
                            $newid[]=$q['id'];
                        }
                        $res['uid']=!empty($newid)? $newid:$uid;

                        break;
                     //常务
                    case 'J':
                        //查询理事-》查询省 ->查询市->查询县

                        $late=$this->late($user['code']); //理事
                        foreach ($late as $k=>$v){
                            $code[]=$v['code'];
                        }
                        $late1=$this->late($code); //省代code
                        foreach ($late1 as $t=>$m){
                            $code1[]=$m['code'];
                        }
                        $late2=$this->late($code1); //市代
                        foreach ($late2 as $p=>$q){
                            $code2[]=$q['code'];
                        }
                        $late3=$this->late($code2); //县代
                        foreach ($late3 as $a=>$b){
                            $newid[]=$b['id'];
                        }

                        $res['uid']=!empty($newid)? $newid:$uid;

                        break;
                    default :
                        $res['uid']=$uid;


                }
             return $res;



    }

    /**
     * 通过code 查询下级id code  username
     * $codes=array() or string
     */
    public  function  late($code){

        $xs=$this->getORM()
            ->select('id,code,username')
            ->where('code_up',$code)
            ->fetchAll();
        return $xs;

    }

    /**  县代理为最低等级 不需要
     * 查询当前用户下面所有的代理id 姓名 按等级展示
     */
    public  function  get_us($uid){

        $res=array();
        $user=$this->getORM()
            ->where(array('id' => $uid, ))
            ->fetchOne();

        if($user){

            //常务J-理事L-省代D-市代B-县代G
            switch($user['level_code']){

                //县代理
                case 'G':
                    //$res['uid']=$uid;

                    break;
                //市代理
                case  'B':
                    //$sql="select id from m_user where  code_up='{$user['code']}'"  ;
                    $late=$this->late($user['code']);

                    $res=$late;

                    break;
                //省代理
                case   'D':
                    //查询市代理再查询县代理

                    $late=$this->late($user['code']);
                    foreach ($late as $m=>$n){

                        $late[$m]['next']=$this->late($n['code']);


                    }
                    $res=$late;


                    break;
                //理事
                case 'L':
                    //查询省 ->查询市->查询县
                    $late=$this->late($user['code']); //省代code
                    foreach ($late as $k=>$v){

                        $next=$this->late($v['code']);

                        foreach ($next as $m=>$n){
                            $next[$m]['next']=$this->late($n['code']);
                        }


                        $late[$k]['next']=$next;
                    }
                    $res=$late;

                    break;
                //常务
                case 'J':
                    //查询理事-》查询省 ->查询市->查询县
                    $late=$this->late($user['code']); //省代code
                    foreach ($late as $k=>$v){

                        $next=$this->late($v['code']);

                        foreach ($next as $m=>$n){
                            $next1=$this->late($n['code']);

                            foreach ($next1 as $q=>$w){

                                $next1[$q]['next']=$this->late($w['code']);
                            }

                            $next[$m]['next']=$next1;
                        }


                        $late[$k]['next']=$next;
                    }

                    $res=$late;


                    break;
               // default :
                    //$res['uid']=$newid;


            }
            return $res;


        }
    }






    public function getListItems($state, $page, $perpage) {
        return $this->getORM()
            ->select('*')
            ->where('state', $state)
            ->order('post_date DESC')
            ->limit(($page - 1) * $perpage, $perpage)
            ->fetchAll();
    }

    public function getListTotal($state) {
        $total = $this->getORM()
            ->where('state', $state)
            ->count('id');

        return intval($total);
    }


}
