<?php

namespace App\Repositories;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegisterRepository
{
    public function checkDuplicateUser($mobile)
    {
        $exists = DB::table('users')
            ->where('username', $mobile)
            ->exists();

        return $exists ? 2 : 1;
    }

    public function createNewRegistration(
        array $usersData,
        array $customerEmp,
        array $companyArr
    ) {
        $userExists = DB::table('users')
            ->where('username', $usersData['username'])
            ->exists();

        if ($userExists) {
            return 0;
        }

        // Insert into users table
        $usersTableId = DB::table('users')->insertGetId($usersData);

        // Insert into customer_employee table
        DB::table('customer_employee')->insert($customerEmp);

        // Insert into corporate_companies table
        DB::table('corporate_companies')->insert($companyArr);

        $data = [
            'user_id'       => $usersData['user_id'],
            'id'            => $usersTableId,
            'fullName'      => $usersData['full_name'],
            'email'         => $usersData['email'],
            'user_group'    => $usersData['user_group'],
            'username'      => $usersData['username'],
            'isReset'       => $usersData['is_reset'],
            'isActive'      => $usersData['is_active'],
            'panelType'     => $usersData['panel_type'],
            'userType'      => $usersData['user_type_code'],
            'comapny_code'  => $companyArr['company_code'], // keeping original key name
            'customer_type' => config('constants.INDIVIDUAL_CUST'),
            'validated'     => true,
        ];

        // Equivalent of CI session->set_userdata()
        Session::put($data);

        // Equivalent of set_login_cookie('1')
        //set_login_cookie('1');

        return 1;
    }
}