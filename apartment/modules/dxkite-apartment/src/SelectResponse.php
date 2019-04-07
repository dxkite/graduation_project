<?php
namespace dxkite\apartment\response;

use dxkite\apartment\provider\ApartmentProvider;
use dxkite\apartment\controller\UserController;

/**
 * 选择宿舍
 */
class SelectResponse extends BaseActionResponse
{
    public function onBinded()
    {
        $view = $this->view('select');
        $user = (new UserController)->getByUser($this->visitor->getId());
        $prv =new ApartmentProvider;
        $prv->loadFromContext($this->context);
        $data = $prv->queryRooms();
        if ($data) {
            $view->set('data',json_encode($data));
        }
        $view->set('title',__('$0 - $1 $2 - 选择宿舍',$user['name'],$user['major'],$user['sex']));
        return $view;
    }
}
