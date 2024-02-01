<?php

namespace App\Helpers;

class Helper {

    public static $superUserEmail = 'super@sekolahbasic.sch.id';

    public static $userDependOnRoleMainTeacher = 'main_teacher';
    public static $userDependOnRoleTeacherSubject = 'teacher_subject';

    public static function getSchoolYears() :array{
        $start = 2023;
        $range = 10;
        $data = [];
        for ($i=1; $i <= $range; $i++) { 
            $data[$i] = $start.'/'.$start+1;
            $start++;
        }
        return $data;
    }
    public static function getSchoolYearById($id) :string{
        return self::getSchoolYears()[$id];
    }
    public static function getTerms() :array{
        return [
            '1' => 'First Semester',
            '2' => 'Second Semester',
        ];
    }
    public static function getTermById($id) :string{
        return self::getTerms()[$id];
    }
    
    public static function getCompanies() :array
    {
        return [
            1 =>'TK BASIC 1',
            2 =>'TK BASIC 2',
            3 =>'SD BASIC 1',
            4 =>'SD BASIC 2',
            5 =>'SMP BASIC 1',
            6 =>'SMP BASIC 2',
            7 =>'SMA BASIC 1',
            8 =>'SMA BASIC 2',
        ];
    }
    public static function getGenders() :array
    {
        return [
            1 =>'Perempuan',
            2 =>'Laki-laki'
        ];
    }

    public static function getReligions() :array
    {
        return [
            1 => 'Kristen',
            2 => 'Katolik',
            3 => 'Hindu',
            4 => 'Buddha',
            5 => 'Konghucu',
            6 => 'Islam'
        ];
    }

    public static function getGenderById($id){
        return self::getGenders()[$id];
    }
    public static function getReligionById($id){
        return self::getReligions()[$id];
    }

}
