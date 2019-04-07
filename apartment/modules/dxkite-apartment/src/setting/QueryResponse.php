<?php
namespace dxkite\apartment\response\setting;

use suda\framework\Request;
use dxkite\apartment\controller\UserController;

class QueryResponse extends \support\setting\response\SettingResponse
{
    /**
     * 添加管理
     *
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $view = $this->view('setting/query');
        if ($request->hasPost()) {
            $idcard = $request->post('query');
            if ($idcard && $student = (new UserController)->getByIdCard($idcard)) {
                $view->set('student', $student);
                $view->set('query', $idcard);
                $check = substr($student['exam_number'], -4, 4).substr($student['idcard'], -4, 4);
                $view->set('password', $check);
            }
        }
        return $view;
    }
}
