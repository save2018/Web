<?php
namespace App\Domain;

use App\Model\User_session as ModelUser_session;

class User_session {

    public function insert($newData) {
        $newData['post_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelUser_session();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelUser_session();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelUser_session();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelUser_session();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelUser_session();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }
}
