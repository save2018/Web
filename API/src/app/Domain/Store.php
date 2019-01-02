<?php
namespace App\Domain;

use App\Model\Config;
use App\Model\Store as ModelStore;

use App\Model\Wechat as ModelWchat;

use App\Model\Config as ModelConfig;

class Store {


    /**查询店铺总数 **/
    public function get_store_count($us){

        $model = new ModelStore();

        $model_config=new ModelConfig();

        $show_shenhe=$model_config->show_shenhe();


        if($us['uid']){
            return $model->get_store_count($us['uid'],$show_shenhe);
        }else{

            return  $model->get_store_count_wx($us['wxid'],$us['did'],$show_shenhe);
        }

    }


    public function insert($newData) {
        $newData['add_time'] =$_SERVER['REQUEST_TIME']; //date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelStore();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelStore();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelStore();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelStore();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage,$keyword) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelStore();

        $model_config=new ModelConfig();

        $show_shenhe=$model_config->show_shenhe();

        $items = $model->getListItems($state, $page, $perpage,$keyword,$show_shenhe);
        $total = $model->getListTotal($state,$keyword,$show_shenhe);
        $total_page=ceil($total/$perpage);

        if($total_page<=intval($page)){
            $more=0;
        }else{
            $more=1;
        }
        $paged=array('page'=>$page,'size'=>$perpage,'more'=>$more);

         $Wechat_model=new ModelWchat();
        foreach ($items as $k=>$v){
            if($v['openid']){
                $items[$k]['nickName']=$Wechat_model->get_nickName($v['openid']);
            }

        }

        $rs['items'] = $items;
        $rs['total'] = $total;
        $rs['paged'] =$paged;

        return $rs;
    }

    /**
     * @param $array
     */
    public  function  get_xyall($array){

        $model = new ModelStore();

        $model_config=new ModelConfig();

        $show_shenhe=$model_config->show_shenhe();

        if($array['uid']){
            return $model->get_xyall($array['uid'],$show_shenhe);
        }else{

            return  $model->get_xy_wx($array['wxid'],$array['did'],$show_shenhe);
        }


    }
    public  function  strict_store($city,$province){
        $model = new ModelStore();
        return $model->strict_store($city,$province);
    }
}
