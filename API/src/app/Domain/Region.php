<?php
namespace App\Domain;

use App\Model\Region as ModelRegion;


class Region {


    /**查询店铺总数 **/
    public function get_region(){
        $model = new ModelRegion();
        return $model->get_region();
    }


    public function insert($newData) {
       // $newData['post_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelRegion();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelRegion();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelRegion();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelRegion();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelRegion();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }

    public  function  region_id($name){
        $model = new ModelRegion();
        return $model->region_id($name);
    }
}
