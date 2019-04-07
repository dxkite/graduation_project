<?php
namespace dxkite\apartment\controller;

use suda\orm\DataSource;
use dxkite\apartment\Setting;
use dxkite\apartment\table\StudentTable;
use dxkite\apartment\table\ApartmentTable;

/**
 * 公寓选择控制器
 */
class ApartmentController
{

    /**
     * 公寓表操作
     *
     * @var ApartmentTable
     */
    protected $apartment;

    /**
     * 学生表
     *
     * @var StudentTable
     */
    protected $student;

    public function __construct()
    {
        $this->apartment = new ApartmentTable;
        $this->student = new StudentTable;
    }

    /**
     * 判断宿舍是否被选
     *
     * @param array $room
     * @return boolean
     */
    public function isSelected(array $room)
    {
        if ($data = $this->apartment->read(['user'])
            ->where([
                'build' => $room['build'],
                'floor' => $room['floor'],
                'room' => $room['room'],
                'bed' => $room['bed']
            ])->one()) {
            if (null === $data['user']) {
                return false;
            }
        }
        return true;
    }

    /**
     * 选择宿舍
     *
     * @param integer $user
     * @param array $room
     * @param string $ip
     * @return bool
     */
    public function select(int $user, array $room, string $ip):bool
    {
        $this->apartment->write('user', null)->where(['user' => $user])->ok();
        return
        $this->student->write('selected', 1)->where(['user' => $user])->ok()
        &&
        $this->apartment->write([
            'user' => $user,
            'time' => time(),
            'ip' => request()->ip()
        ])
        ->where([
            'build' => $room['build'],
            'floor' => $room['floor'],
            'room' => $room['room'],
            'bed' => $room['bed']
        ])->ok();
    }

    /**
     * 获取选择的宿舍
     *
     * @param integer $user
     * @return array
     */
    public function get(int $user)
    {
        return $this->apartment->read('*')
        ->where([
            'user' => $user
        ])->one();
    }

    /**
     * 是否关闭选择
     *
     * @return boolean
     */
    public function isClose()
    {
        $setting = new Setting('apartment');
        $start = $setting->get('apartment_start_time');
        $end = $setting->get('apartment_end_time');
        if ($start && $end) {
            if (time() > $start && time() < $end) {
                return false;
            }
        } else {
            return false;
        }
        return false;
    }
}
