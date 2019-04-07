<?php
namespace dxkite\apartment\provider\setting;

use Exception;
use dxkite\apartment\Setting;
use support\openmethod\parameter\File;
use dxkite\apartment\controller\ExcelController;
use support\setting\provider\UserSessionAwareProvider;

class ApartmentProvider extends UserSessionAwareProvider
{
   

    /**
     * @acl apartment.modify
     *
     * @return bool
     */
    public function setMustPay()
    {
        $setting = new Setting('apartment');
        $setting->set('apartment_must_pay', !$setting->get('apartment_must_pay'));
        $setting->save();
        return $setting->get('apartment_must_pay');
    }
   
    /**
     * @acl apartment.modify
     *
     * @param float $arrearage
     * @return bool
     */
    public function setMinPay(float $arrearage)
    {
        $setting = new Setting('apartment');
        $setting->set('apartment_min_pay', $arrearage * 100);
        return $setting->save();
    }

    /**
     * @acl apartment.modify
     *
     * @param string $start
     * @param string $end
     * @return void
     */
    public function setOpenTime(string $start, string $end)
    {
        $setting = new Setting('apartment');
        $start = date_create_from_format('Y-m-d H:i:s', $start);
        $end = date_create_from_format('Y-m-d H:i:s', $end);
        if ($start && $end) {
            $setting->set('apartment_start_time', $start->getTimestamp());
            $setting->set('apartment_end_time', $end->getTimestamp());
            return $setting->save();
        }
        return false;
    }

    /**
     *
     * @acl apartment.modify
     *
     * @param File $students
     * @return void
     */
    public function uploadStudents(File $students)
    {
        $excel = new ExcelController;
        return $excel->uploadStudentInfo($students->getPathname());
    }

    /**
     *
     * @acl apartment.modify
     *
     * @param File $pays
     * @return void
     */
    public function uploadPays(File $pays)
    {
        $excel = new ExcelController;
        return $excel->uploadPayInfo($pays->getPathname());
    }

    /**
     *
     * @acl apartment.modify
     *
     * @param File $builds
     * @return void
     */
    public function uploadBuilds(File $builds)
    {
        $excel = new ExcelController;
        return $excel->uploadBuildInfo($builds->getPathname());
    }
}
