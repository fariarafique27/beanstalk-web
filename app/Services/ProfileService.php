<?php
namespace App\Services;
use \Illuminate\Http\RedirectResponse;

class ProfileService extends GuzzleApiService
{
    public function dashboard(){

        $token = session()->get('user.token');
        logger('ProfileService dashboard called. Token status: ' . ($token ? 'Present' : 'Null'));

        if($token==null) {
            logger('Token is null. Redirecting to login.');
            return redirect()->route('login');
        }
        $role = session()->get('user.role');
        logger('User role found: ' . $role);

        if(session()->get('user.role') == 'Super Admin'){
            logger('Executing Super Admin dashboard branch.');
            $response = $this->get('super-admin/dashboard');

            if ($response instanceof RedirectResponse) {
                return $response;
            }
            if (!$response['success']) {
                logger()->error('Super Admin API failed: ' . ($response['message'] ?? 'Unknown error'));
                session()->forget('user');
                session()->forget('decrypt_token');
                return redirect()->back()
                ->withErrors(isset($response['errors']) ? $response['errors'] : ['error'=> $response['message']])
                ->withInput();
            }

            $data = $response['data'];

            return view('superAdmin.dashboard', compact('data'));
        }
        logger('Executing Regular Company Admin dashboard branch.');
        $permissions = session('permissions', []);

        // if (!in_array('dashboard.read', $permissions, true)) {
        //     return view('compaines.dashboard.dashboard');
        // }

        $dashboardCount = $this->get('dashboard');
        if ($dashboardCount instanceof \Illuminate\Http\RedirectResponse) {
            return $dashboardCount;
        }
        if (!$dashboardCount['success']) {
            session()->forget('user');
            session()->forget('decrypt_token');
            return redirect()->back()
                ->withErrors(isset($dashboardCount['errors']) ? $dashboardCount['errors']  : ['error' => $dashboardCount['message']])
                ->withInput();
        }

        $upcomingHolidaysResponse = $this->get('holidays/upcoming');
        if ($upcomingHolidaysResponse instanceof \Illuminate\Http\RedirectResponse) {
            return $upcomingHolidaysResponse;
        }
        if (!$upcomingHolidaysResponse['success']) {
            return redirect()->back()
                ->withErrors(isset($upcomingHolidaysResponse['errors']) ? $upcomingHolidaysResponse['errors']  : ['error' => $upcomingHolidaysResponse['message']])
                ->withInput();
        }


        $graphData = [];
        if (in_array('employee.read', $permissions, true)) {
            $countEmp = $this->get('count/employee/departments');
            if ($countEmp instanceof \Illuminate\Http\RedirectResponse) {
                return $countEmp;
            }
            if (!$countEmp['success']) {
                return redirect()->back()
                    ->withErrors(isset($countEmp['errors']) ? $countEmp['errors']  : ['error' => $countEmp['message']])
                    ->withInput();
            }
            $graphData = $countEmp['data']['dataGraph'] ?? [];
        }

        $payslips = [];
        if (in_array('my_payslip.read', $permissions, true)) {
            $myPayslipResponse = $this->get('myPayslip');
            if (!($myPayslipResponse instanceof \Illuminate\Http\RedirectResponse) && $myPayslipResponse['success']) {
                $payslips = array_slice($myPayslipResponse['data']['payslips'] ?? [], 0, 3);
            }
        }

        $dataCount        = $dashboardCount['data']['dashboard'] ?? [];
        $birthdays        = $dashboardCount['data']['dashboard']['birthdays'] ?? [];
        // $employees        = $dashboardCount['data']['dashboard']['employees'] ?? [];
        $workAnniversary  = $dashboardCount['data']['dashboard']['workAnniversary'] ?? [];
        $myEmployee       = $dashboardCount['data']['dashboard']['my_profile'] ?? null;
        $upcomingHolidays = $upcomingHolidaysResponse['data']['upcoming_holidays'] ?? [];

        return view('compaines.dashboard.dashboard', compact('dataCount', 'graphData', 'birthdays', 'workAnniversary', 'upcomingHolidays', 'payslips', 'myEmployee'));

    }
}