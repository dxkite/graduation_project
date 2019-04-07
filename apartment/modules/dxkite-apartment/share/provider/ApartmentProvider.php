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

    // public function queryRooms():?array
    // {
    //     $userId= get_user_id();
    //     $user=$this->table->select('*', ['user'=>$userId])->fetch();
    //     if ($user) {
    //         $apartments = $this->apartment->setFields(['build','floor','room','bed'])->listWhere('`sex`=:sex and `major`=:major and `user` is null', [
    //             'sex'=>$user['sex'],
    //             'major'=>$user['major']
    //         ]);
    //         if ($apartments) {
    //             return self::treeThis($apartments);
    //         }
    //         return null;
    //     } else {
    //         throw new \Exception(__('用户未绑定'), -1);
    //     }
    // }

    // public function select(array $room, string $code)
    // {
    //     $ap =new ApartmentController;
    //     $u = new UserController;

    //     if ($ap->isClose()) {
    //         throw new \Exception(__('系统关闭'), -2);
    //     }
    //     if (!$u->selectable()) {
    //         throw new \Exception(__('当前用户不可选择'), -3);
    //     }
    //     if ($ap->isSelected($room)) {
    //         throw new \Exception(__('当前宿舍刚刚被选中'), -4);
    //     }
    //     if (HumanCode::check($code)) {
    //         return $ap->select(get_user_id(), $room);
    //     } else {
    //         throw new \Exception(__('验证码错误'), -1);
    //     }
    // }

    // public function signout()
    // {
    //     visitor()->signout();
    // }

    // /**
    //  * @acl apartment.modify
    //  *
    //  * @return void
    //  */
    // public function setMustPay()
    // {
    //     setting_set('apartment_must_pay', ! setting('apartment_must_pay'));
    //     return  setting('apartment_must_pay');
    // }
   
    // /**
    //  * @acl apartment.modify
    //  *
    //  * @param float $arrearage
    //  * @return void
    //  */
    // public function setMinPay(float $arrearage)
    // {
    //     setting_set('apartment_min_pay', $arrearage * 100);
    //     return setting('apartment_min_pay') == $arrearage * 100;
    // }

    // /**
    //  * @acl apartment.modify
    //  *
    //  * @param string $start
    //  * @param string $end
    //  * @return void
    //  */
    // public function setOpenTime(string $start, string $end)
    // {
    //     if (date_create_from_format('Y-m-d H:i:s', $start) && date_create_from_format('Y-m-d H:i:s', $end)) {
    //         setting_set('apartment_start_time', $start);
    //         setting_set('apartment_end_time', $end);
    //         return true;
    //     }
    //     return false;
    // }

    // /**
    //  *
    //  * @acl apartment.modify
    //  *
    //  * @param File $students
    //  * @return void
    //  */
    // public function uploadStudents(File $students)
    // {
    //     $excel=new ExcelController;
    //     return $excel->uploadStudentInfo($students->getPath());
    // }

    // /**
    //  *
    //  * @acl apartment.modify
    //  *
    //  * @param File $pays
    //  * @return void
    //  */
    // public function uploadPays(File $pays)
    // {
    //     $excel=new ExcelController;
    //     return $excel->uploadPayInfo($pays->getPath());
    // }

    // /**
    //  *
    //  * @acl apartment.modify
    //  *
    //  * @param File $builds
    //  * @return void
    //  */
    // public function uploadBuilds(File $builds)
    // {
    //     $excel=new ExcelController;
    //     return $excel->uploadBuildInfo($builds->getPath());
    // }

    // private function treeThis(array $apart)
    // {
    //     $tree=[];
    //     foreach ($apart as $item) {
    //         self::add($tree, $item);
    //     }
    //     return $tree;
    // }

    // private function add(array &$apart, array $in)
    // {
    //     // TODO 递归实现
    //     $a=self::seekIndex($apart, $in['build']);
    //     $apart[$a]['value']=$in['build'];
    //     $apart[$a]['text']='第'.$in['build'].'栋';
    //     $b=self::seekIndex($apart[$a]['children']??[], $in['floor']);
    //     $apart[$a]['children'][$b]['text']=$in['floor'].'楼';
    //     $apart[$a]['children'][$b]['value']=$in['floor'];
    //     $c=self::seekIndex($apart[$a]['children'][$b]['children']??[], $in['room']);
    //     $apart[$a]['children'][$b]['children'][$c]['text']=$in['room'].'房';
    //     $apart[$a]['children'][$b]['children'][$c]['value']=$in['room'];
    //     $d=self::seekIndex($apart[$a]['children'][$b]['children'][$c]['children']??[], $in['bed']);
    //     $apart[$a]['children'][$b]['children'][$c]['children'][$d]['text']=$in['bed'].'床位';
    //     $apart[$a]['children'][$b]['children'][$c]['children'][$d]['value']=$in['bed'];
    // }

    // private function seekIndex(array $input, int $value)
    // {
    //     foreach ($input as $index=>$item) {
    //         if ($item['value']==$value) {
    //             return $index;
    //         }
    //     }
    //     return count($input);
    // }
}
