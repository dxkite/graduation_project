<?php
namespace dxkite\apartment\response\setting;

use suda\framework\Request;

use suda\framework\debug\Debug;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use dxkite\apartment\table\StudentTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use dxkite\apartment\table\ApartmentTable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Helper\Sample;

class DownloadResponse extends \support\setting\response\SettingResponse
{
    /**
     * 添加管理
     *
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $helper = new Sample();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('DXkite')
            ->setLastModifiedBy('DXkite')
            ->setTitle('Office 2007 XLSX Student Apartment Info Document')
            ->setSubject('Office 2007 XLSX Student Apartment Info Document')
            ->setDescription('This File Content The Infomation About  Student Apartment')
            ->setKeywords('DXkite Student Apartment')
            ->setCategory('Datafile');
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '学号')
            ->setCellValue('B1', '考生号')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '性别')
            ->setCellValue('E1', '身份证号')
            ->setCellValue('F1', '专业')
            ->setCellValue('G1', '班级')
            ->setCellValue('H1', '楼宇')
            ->setCellValue('I1', '楼层')
            ->setCellValue('J1', '房间号')
            ->setCellValue('K1', '床位号')
            ->setCellValue('L1', '备注')
            ;
        
        $timeStart = microtime(true);

        $user = new StudentTable;
        $apartment = new ApartmentTable;

        // 导出标志置0
        $user->write(['export' => 0])->where('1');

        $rows = $apartment->read('*')->scroll();
        $index = 0;
        $id = 1;
        while ($data = $rows->one()) {
            $id += 1;
            // 未选择则忽略
            if (null === $data['user']) {
                $student = null;
            } else {
                $student = $user->read('*')->where(['user' => $data['user']])->one();
            }

            if (null === $student) {
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('H'.$id, $data['build'])
                ->setCellValue('I'.$id, $data['floor'])
                ->setCellValue('J'.$id, $data['room'])
                ->setCellValue('K'.$id, $data['bed'])
                ->setCellValue('L'.$id, '未选择');
            } else {
                // 标记
                $user->write(['export' => 1])->where(['id' => $student['id']])->ok();
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValueExplicit('A'.$id, $student['number'], DataType::TYPE_STRING)
                ->setCellValueExplicit('B'.$id, $student['exam_number'], DataType::TYPE_STRING)
                ->setCellValue('C'.$id, $student['name'])
                ->setCellValue('D'.$id, $student['sex'])
                ->setCellValueExplicit('E'.$id, $student['idcard'], DataType::TYPE_STRING)
                ->setCellValue('F'.$id, $student['major'])
                ->setCellValue('G'.$id, $student['class'])
                ->setCellValue('H'.$id, $data['build'])
                ->setCellValue('I'.$id, $data['floor'])
                ->setCellValue('J'.$id, $data['room'])
                ->setCellValue('K'.$id, $data['bed'])
                ->setCellValue('L'.$id, '自选');
            }
            $index = $id;
        }
        
        $numberGet = $user->read('count("id") as count')->where(['export' => 1])->one();
        $number = $numberGet['count'];
        $allGet = $user->read('count("id") as count')->one();
        $all = $allGet['count'];

        $exports = $index - 1;
        $index += 1;


        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A' . $index, __('一共导出了$0条数据，共$1人，导出$2人', $exports, $all, $number))
        ->setCellValue('A' . ($index + 1), __('导出时间：$0，一共耗时 $1 秒，内存峰值：$2', date('Y-m-d H:i:s', time()), microtime(true) - $timeStart, Debug::formatBytes(memory_get_peak_usage())));
        
        $spreadsheet->getActiveSheet()->setTitle('公寓选择信息导出表');
        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(SUDA_DATA.'/output.xlsx');
        $this->response->setHeader('content-disposition', 'attachment;filename="公寓选择信息导出表_'.date('YmdHis').'.xlsx"');
        $this->response->setHeader('cache-control', 'max-age=0');
        $this->response->sendFile(SUDA_DATA.'/output.xlsx');
    }
}
