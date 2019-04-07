<?php
namespace dxkite\apartment\provider;

use Exception;
use support\setting\UserSession;
use support\setting\VerifyImage;
use dxkite\apartment\table\StudentTable;
use dxkite\apartment\table\ApartmentTable;
use dxkite\apartment\controller\UserController;
use dxkite\apartment\controller\ExcelController;
use dxkite\apartment\controller\ApartmentController;
use dxkite\openclient\provider\VisitorAwareProvider;

class ApartmentProvider extends VisitorAwareProvider
{

    /**
     * 用户控制器
     *
     * @var UserController
     */
    protected $user;

    /**
     * ApartmentController
     *
     * @var ApartmentController
     */
    protected $apartment;

    public function __construct()
    {
        $this->user = new UserController;
        $this->apartment = new ApartmentController;
    }

    public function bind(string $idcard, string $password, string $code): bool
    {
        $verify = new VerifyImage($this->context, 'apartment');
        if ($verify->checkCode($code) === false) {
            throw new Exception('验证码错误', 50011);
        }
        $data = $this->user->getByIdCard($idcard);
        if ($data === null) {
            throw new \Exception('身份证号不存在，请联系管理', 50012);
        }
        if ($data['user'] !== null) {
            throw new \Exception('该学号已经被绑定', 50013);
        }
        $check = substr($data['exam_number'], -4, 4).substr($data['idcard'], -4, 4);
        if (strtoupper($password) === $check) {
            if ($this->user->bind($this->visitor->getId(), $data['id'])) {
                return true;
            }
            throw new \Exception('系统繁忙，请稍后重试', 50014);
        } else {
            throw new \Exception('绑定验证失败', 50015);
        }
    }

    public function queryRooms():?array
    {
        $userId = $this->visitor->getId();
        $user = $this->user->getByUser($userId);
        if ($user) {
            if ($apartments = $this->apartment->query($user['major'], $user['sex'])) {
                return $this->treeThis($apartments);
            }
            return null;
        } else {
            throw new \Exception('用户未绑定', 50001);
        }
    }

    public function select(array $room, string $code)
    {
        $this->apartment = new ApartmentController;
        $this->user = new UserController;

        if ($this->apartment->isClose()) {
            throw new \Exception('系统关闭', 50030);
        }
        if (!$this->user->selectable($this->visitor->getId())) {
            throw new \Exception('当前用户不可选择', 50031);
        }
        if ($this->apartment->isSelected($room)) {
            throw new \Exception('当前宿舍刚刚被选中', 50032);
        }
        $verify = new VerifyImage($this->context, 'apartment');
        if ($verify->checkCode($code) === false) {
            throw new Exception('验证码错误', 50033);
        }
        return $this->apartment->select($this->visitor->getId(), $room, $this->request->getRemoteAddr());
    }

 
    private function treeThis(array $apart)
    {
        $tree = [];
        foreach ($apart as $item) {
            $this->add($tree, $item->toArray());
        }
        return $tree;
    }

    private function add(array &$apart, array $in)
    {
        // TODO 递归实现
        $a = $this->seekIndex($apart, $in['build']);
        $apart[$a]['value'] = $in['build'];
        $apart[$a]['text'] = '第'.$in['build'].'栋';
        $b = $this->seekIndex($apart[$a]['children'] ?? [], $in['floor']);
        $apart[$a]['children'][$b]['text'] = $in['floor'].'楼';
        $apart[$a]['children'][$b]['value'] = $in['floor'];
        $c = $this->seekIndex($apart[$a]['children'][$b]['children'] ?? [], $in['room']);
        $apart[$a]['children'][$b]['children'][$c]['text'] = $in['room'].'房';
        $apart[$a]['children'][$b]['children'][$c]['value'] = $in['room'];
        $d = $this->seekIndex($apart[$a]['children'][$b]['children'][$c]['children'] ?? [], $in['bed']);
        $apart[$a]['children'][$b]['children'][$c]['children'][$d]['text'] = $in['bed'].'床位';
        $apart[$a]['children'][$b]['children'][$c]['children'][$d]['value'] = $in['bed'];
    }

    public function signout()
    {
        return UserSession::expire($this->visitor->getId(), $this->group);
    }

    private function seekIndex(array $input, int $value)
    {
        foreach ($input as $index => $item) {
            if ($item['value'] == $value) {
                return $index;
            }
        }
        return count($input);
    }
}
