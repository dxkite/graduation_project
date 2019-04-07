<?php
namespace dxkite\apartment\controller;

use PhpOffice\PhpSpreadsheet\IOFactory;
use dxkite\apartment\table\StudentTable;
use dxkite\apartment\table\ApartmentTable;
use dxkite\apartment\excel\CellIterator;

class ExcelController
{
    public function uploadPayInfo(string $path)
    {
        $table = new StudentTable;
        $iterator = new CellIterator(IOFactory::load($path)->getSheet(0)->getCellCollection(), 'D');
        $i = 0;
      
        foreach ($iterator as $key => $value) {
            if ($key == 0) {
                // 检查表头
                $check = [
                    'A' => '学号',
                    'B' => '姓名',
                    'C' => '班级名称',
                    'D' => '欠费金额'
                ];
                foreach ($value as $id => $value) {
                    if ($check[$id] !== $value) {
                        return false;
                    }
                }
            } else {
                // 部分置0
                if ($table->write(
                    [ 'arrearage' => $value['D'] * 100, ]
                )->where(['number' => $value['A'] ])->ok()) {
                    $i++;
                }
            }
        }
        return $i;
    }

    public function uploadStudentInfo(string $path)
    {
        $table = new StudentTable;
        $iterator = new CellIterator(IOFactory::load($path)->getSheet(0)->getCellCollection(), 'G');
        $i = 0;
        foreach ($iterator as $key => $value) {
            if ($key == 0) {
                $check = [
                    'A' => '学号',
                    'B' => '姓名',
                    'C' => '考生号',
                    'D' => '身份证号',
                    'E' => '性别',
                    'F' => '专业',
                    'G' => '班级'
                ];
                foreach ($value as $id => $value) {
                    if ($check[$id] !== $value) {
                        return -1;
                    }
                }
            } else {
                $value['D'] = strtoupper($value['D']);
                try {
                    if ($table->read('*')->where(['idcard' => $value['D'] ])->one()) {
                        $table->write([
                            'number' => $value['A'],
                            'name' => $value['B'],
                            'exam_number' => $value['C'],
                            'sex' => $value['E'],
                            'major' => $value['F'],
                            'class' => $value['G'],
                        ])->where(['idcard' => $value['D']])->ok();
                    } else {
                        $table->write([
                            'number' => $value['A'],
                            'name' => $value['B'],
                            'exam_number' => $value['C'],
                            'idcard' => $value['D'],
                            'sex' => $value['E'],
                            'major' => $value['F'],
                            'class' => $value['G'],
                        ])->ok();
                    }
                    $i++;
                } catch (\Exception $e) {
                    // noop
                }
            }
        }
        return $i;
    }

    public function uploadBuildInfo(string $path)
    {
        $table = new ApartmentTable;
        $iterator = new CellIterator(IOFactory::load($path)->getSheet(0)->getCellCollection(), 'F');
        $i = 0;
        foreach ($iterator as $key => $value) {
            // debug()->info('upload iterator');
            if ($key == 0) {
                $check = [
                    'A' => '专业',
                    'B' => '楼宇',
                    'C' => '性别',
                    'D' => '楼层',
                    'E' => '房间号',
                    'F' => '床位号'
                ];
                // 检查行数属性是否匹配
                foreach ($value as $id => $value) {
                    if ($check[$id] !== $value) {
                        return false;
                    }
                }
            } else {
                try {
                    $where = [
                        'build' => intval($value['B']),
                        'floor' => intval($value['D']),
                        'room' => intval($value['E']),
                        'bed' => intval($value['F']),
                    ];
                    if (!$table->read('*')->where($where)->one()) {
                        $table->write([
                            'major' => $value['A'],
                            'build' => intval($value['B']),
                            'sex' => $value['C'],
                            'floor' => intval($value['D']),
                            'room' => intval($value['E']),
                            'bed' => intval($value['F']),
                        ]);
                    }
                    $i++;
                    // debug()->info('upload>', $value);
                } catch (\Exception $e) {
                    // debug()->error(get_class($e), $e->getMessage());
                    // return $e->getMessage();
                    // 忽略上传错误
                }
            }
        }
        return $i;
    }
}
