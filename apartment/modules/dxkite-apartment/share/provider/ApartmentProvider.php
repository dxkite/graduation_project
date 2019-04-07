<?php
namespace dxkite\apartment\provider;

use Exception;
use support\setting\VerifyImage;
use dxkite\apartment\table\StudentTable;
use dxkite\apartment\table\ApartmentTable;
use dxkite\apartment\controller\UserController;
use dxkite\apartment\controller\ExcelController;
use dxkite\apartment\controller\ApartmentController;
use dxkite\openclient\provider\VisitorAwareProvider;

class ApartmentProvider extends VisitorAwareProvider
{
    protected $table;
    protected $apartment;

    /**
     * 用户控制器
     *
     * @var UserController
     */
    protected $user;

    public function __construct()
    {
        $this->user = new UserController;
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
            $apartments = $this->apartment->read(['build','floor','room','bed'])->where([
                'sex' => $user['sex'],
                'major' => $user['major'],
                'user' => ['is', null],
            ]);
            if ($apartments) {
                return $this->treeThis($apartments);
            }
            return null;
        } else {
            throw new \Exception('用户未绑定', 50001);
        }
    }

    public function select(array $room, string $code)
    {
        $ap = new ApartmentController;
        $u = new UserController;

        if ($ap->isClose()) {
            throw new \Exception('系统关闭', -2);
        }
        if (!$u->selectable()) {
            throw new \Exception('当前用户不可选择', -3);
        }
        if ($ap->isSelected($room)) {
            throw new \Exception('当前宿舍刚刚被选中', -4);
        }
        $verify = new VerifyImage($this->context, 'apartment');
        if ($verify->checkCode($code) === false) {
            throw new Exception('验证码错误', 50011);
        }
        return $ap->select($this->visitor->getId(), $room);
    }

 
    private function treeThis(array $apart)
    {
        $tree = [];
        foreach ($apart as $item) {
            $this->add($tree, $item);
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
