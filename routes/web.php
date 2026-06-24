<?php

use App\Http\Controllers\Admin\AdminPushNotificationController;
use App\Http\Controllers\Admin\AnniversaryOrBirthdayCardController;
use App\Http\Controllers\Admin\Appointment\AppointmentController;
use App\Http\Controllers\Admin\BlockController;
use App\Http\Controllers\Admin\BlockRoadController;
use App\Http\Controllers\Admin\Building\BuildingAdditionalImagesController;
use App\Http\Controllers\Admin\Building\BuildingController as BuildingBuildingController;
use App\Http\Controllers\Admin\Building\BuildingProfilePictureController;
use App\Http\Controllers\Admin\Corporate_customer\CompanyAttachmentController;
use App\Http\Controllers\Admin\Corporate_customer\CompanyController;
use App\Http\Controllers\Admin\Corporate_customer\CompanyOfficeController;
use App\Http\Controllers\Admin\Corporate_customer\CompanyProfileController;
use App\Http\Controllers\Admin\Corporate_customer\Employee\CompanyEmployeeAttachmentController;
use App\Http\Controllers\Admin\Corporate_customer\Employee\CompanyEmployeeController;
use App\Http\Controllers\Admin\Corporate_customer\Employee\CompanyEmployeeOfficeController;
use App\Http\Controllers\Admin\Corporate_customer\Employee\CompanyEmployeePhotographController;
use App\Http\Controllers\Admin\CRM\CallLogController;
use App\Http\Controllers\Admin\CRM\CallLogCustomerSearch;
use App\Http\Controllers\Admin\Dashboard\DashboardController as DashboardDashboardController;
use App\Http\Controllers\Admin\EmployeeAnniversaryOrBirthdayCardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeEducationController;
use App\Http\Controllers\Admin\EmployeeOfficeController;
use App\Http\Controllers\Admin\Floor\FloorAdditionalImageController;
use App\Http\Controllers\Admin\Floor\FloorController;
use App\Http\Controllers\Admin\Floor\FloorImageController;
use App\Http\Controllers\Admin\HomeService\EmployeeHomeServiceController;
use App\Http\Controllers\Admin\HomeService\HomeServiceAssignToEmployeeController;
use App\Http\Controllers\Admin\HomeService\HomeServiceController as HomeServiceHomeServiceController;
use App\Http\Controllers\Admin\HomeService\RaiseHomeServiceController;
use App\Http\Controllers\Admin\IndividualCustomer\CardRenewController;
use App\Http\Controllers\Admin\IndividualCustomer\IndividualCustomerController;
use App\Http\Controllers\Admin\MasterData\AppointmentService\AppointmentServiceController;
use App\Http\Controllers\Admin\MasterData\HomeService\HomeServiceCategoryController;
use App\Http\Controllers\Admin\MasterData\HomeService\HomeServiceController;
use App\Http\Controllers\Admin\MasterData\HomeService\HomeServiceListController;
use App\Http\Controllers\Admin\MasterData\HomeService\HomeServiceVariantController;
use App\Http\Controllers\Admin\MasterData\AppointmentService\ServiceCategoryController;
use App\Http\Controllers\Admin\MasterData\AppointmentService\ServiceListController;
use App\Http\Controllers\Admin\MasterData\AppointmentService\ServiceVariantController;
use App\Http\Controllers\Admin\MasterData\CallCenterController;
use App\Http\Controllers\Admin\MasterData\CallReasonController;
use App\Http\Controllers\Admin\MasterData\CustomerFeedBackController;
use App\Http\Controllers\Admin\MasterData\ExpenseAdmin\CostCategoryController;
use App\Http\Controllers\Admin\MasterData\ExpenseAdmin\CostHeadController;
use App\Http\Controllers\Admin\MasterData\ExpenseAdmin\ExpenseAdminController;
use App\Http\Controllers\Admin\MasterData\FuelController;
use App\Http\Controllers\Admin\MasterData\MembershipCardController;
use App\Http\Controllers\Admin\MasterData\PackageController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleBrandController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleBrandModelController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleClassController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleColorController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleConditionController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleGroupController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleTypeController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberEductionController;
use App\Http\Controllers\Admin\MemberFamilyMemberController;
use App\Http\Controllers\Admin\MemberIdCardController;
use App\Http\Controllers\Admin\MemberOfficeController;
use App\Http\Controllers\Admin\MemberPhotoController;
use App\Http\Controllers\Admin\MemberSearchController;
use App\Http\Controllers\Admin\MemberWorkingExperieanceController;
use App\Http\Controllers\Admin\MasterData\AreaController;
use App\Http\Controllers\Admin\MasterData\Income\IncomeCategoryController;
use App\Http\Controllers\Admin\MasterData\Income\IncomeController;
use App\Http\Controllers\Admin\MasterData\Income\IncomeHeadController;
use App\Http\Controllers\Admin\MasterData\Vehicle\VehicleController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ModuleGroupController;
use App\Http\Controllers\Admin\NewBoarderController;
use App\Http\Controllers\Admin\Place\PlaceAttachmentController;
use App\Http\Controllers\Admin\Place\PlaceController;
use App\Http\Controllers\Admin\Place\PlaceImageController;
use App\Http\Controllers\Admin\Place\PlaceTimeScheduleController;
use App\Http\Controllers\Admin\ProfilePhotoController;
use App\Http\Controllers\Admin\Quotation\QuotationController as QuotationQuotationController;
use App\Http\Controllers\Admin\RMAssign\RMAssignController;
use App\Http\Controllers\Admin\Room\RoomAdditionalImageController;
use App\Http\Controllers\Admin\Room\RoomController;
use App\Http\Controllers\Admin\Room\RoomImageController;
use App\Http\Controllers\Admin\Seat\SeatAdditionalImageController;
use App\Http\Controllers\Admin\Seat\SeatController;
use App\Http\Controllers\Admin\Seat\SeatImageController;
use App\Http\Controllers\Admin\Seat\SeatTypeController;
use App\Http\Controllers\Admin\SubModuleController;
use App\Http\Controllers\Admin\UserGroupController;
use App\Http\Controllers\Admin\WorkingExperienceController;
use App\Http\Controllers\Admin\Workshop\AttachmentController;
use App\Http\Controllers\Admin\Workshop\GeneralInfoController;
use App\Http\Controllers\Admin\Workshop\ImageController;
use App\Http\Controllers\Admin\Workshop\TimeScheduleController;
use App\Http\Controllers\Admin\Workshop\WorkshopController;
use App\Http\Controllers\Admin\Workshop\WorkshopVehicleTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Vendor\VendorAdditionalImageController;
use App\Http\Controllers\Admin\Vendor\VendorController;
use App\Http\Controllers\Admin\Vendor\VendorImageController;
use App\Http\Controllers\Admin\Workshop\ServiceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


Route::get('/', function () {
    return view('welcome');
});
//Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/new-register', [RegisteredUserController::class, 'doRegistration'])->name('new-register');
Route::post('/new-register', [RegisteredUserController::class, 'doRegistration'])->name('new-register');
Route::post('/registration/check-duplicate-user', [RegisteredUserController::class, 'checkDuplicateUser'])->name('registration.checkDuplicateUser');
Route::get('/registration/get-current-dt-tm', [RegisteredUserController::class, 'getCurrentDtTm'])->name('registration.getCurrentDtTm');
Route::post('/registration/check-verification-code', [RegisteredUserController::class, 'checkVerificationCode'])->name('registration.checkVerificationCode');
Route::post('/registration/create-new-registration', [RegisteredUserController::class, 'createNewRegistration'])->name('registration.createNewRegistration');

//Route::prefix('admin')->middleware('auth')->group(function () {
Route::middleware(['auth', 'panel:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('users',[UserController::class, 'index'])->name('admin.users.index');
    Route::get('users/{id}',[UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('user/{id}',[UserController::class, 'update'])->name('admin.users.update');
    Route::get('/users-data', [UserController::class, 'getUsers'])->name('users.data.index');

    Route::get('user-groups',[UserGroupController::class, 'index'])->name('admin.user-groups.index');
    Route::get('user-groups-data', [UserGroupController::class, 'getUserGroups'])->name('user-groups.data.index');
    Route::get('user-group',[UserGroupController::class, 'create'])->name('admin.user-groups.create');
    Route::post('user-group',[UserGroupController::class, 'storeOrUpdate'])->name('admin.user-groups.store');
    Route::get('user-groups/{id}',[UserGroupController::class, 'edit'])->name('admin.user-groups.edit');
    Route::put('user-group/{id}', [UserGroupController::class, 'storeOrUpdate'])->name('admin.user-groups.update');
    Route::patch('user-groups/{id}/status', [UserGroupController::class, 'updateStatus'])->name('admin.user-groups.status');
    Route::delete('user-groups/{id}', [UserGroupController::class, 'destroy'])->name('admin.user-groups.destroy');

    Route::get('module-group',[ModuleGroupController::class, 'index'])->name('admin.module-group.index');
    Route::get('module-group-data', [ModuleGroupController::class, 'getModuleGroupData'])->name('module-group.data.index');
    Route::get('module-group-create',[ModuleGroupController::class, 'create'])->name('admin.module-group.create');
    Route::post('module-group-store',[ModuleGroupController::class, 'store'])->name('admin.module-group.store');
    Route::get('users/module-group-show', [ModuleGroupController::class, 'show'])->name('admin.module-group.show');
    Route::get('users/module-group-edit/{id}',[ModuleGroupController::class, 'edit'])->name('admin.module-group.edit');
    Route::put('module-group-update/{id}',[ModuleGroupController::class, 'update'])->name('admin.module-group.update');
    Route::delete('/module-group-destroy/{id}', [ModuleGroupController::class, 'destroy'])->name(name: 'admin.module-group.destroy');

    Route::resource('modules', ModuleController::class)->names('admin.modules');
    Route::get('modules-data', [ModuleController::class, 'getModulesData'])->name('modules.data.index');
    Route::get('module-groups/{panel}', [ModuleController::class, 'selectModuleData'])->name('select.modules.data');

    Route::resource('sub-modules', SubModuleController::class)->names('admin.sub-modules');
    Route::get('/sub-modules-data', [SubModuleController::class, 'getSubModulesData'])->name('sub-modules.data.index');

    
    /*===============Expense Category==================*/
    Route::get('master-data/expense', [ExpenseAdminController::class,'index'])->name('admin.module.master-data.expense');
    Route::resource('master-data/expense/cost-category', CostCategoryController::class)->names('admin.module.master-data.expense-category');
    Route::post('master-data/expense/cost-category/{code}', [CostCategoryController::class, 'toggle'])->name('admin.module.master-data.expense-category.toggle');
    Route::resource('master-data/expense/cost-head', CostHeadController::class)->names('admin.module.master-data.expense-head');
    Route::post('master-data/expense/cost-category/{code}', [CostHeadController::class, 'toggle'])->name('admin.module.master-data.expense-head.toggle');

    /*===============Expense Category==================*/
    Route::get('master-data/income', [IncomeController::class,'index'])->name('admin.module.master-data.income');
    Route::resource('master-data/income/income-category', IncomeCategoryController::class)->names('admin.module.master-data.income-category');
    Route::post('master-data/income/income-category/{code}', [IncomeCategoryController::class, 'toggle'])->name('admin.module.master-data.income-category.toggle');
    Route::resource('master-data/income/income-head', IncomeHeadController::class)->names('admin.module.master-data.income-head');
    Route::post('master-data/income/income-category/{code}', [IncomeHeadController::class, 'toggle'])->name('admin.module.master-data.income-head.toggle');


    /*===============Employee Module Route==================*/
    Route::resource('employees', EmployeeController::class)->names('admin.employee.module');
    Route::get('employee-data', [EmployeeController::class, 'getEmployeeData'])->name('admin.employee.data.index');
    Route::patch('employee/{id}/status', [EmployeeController::class, 'updateStatus'])->name('admin.employee.status');

    Route::get('employees/employee-office-info/{id}', [EmployeeOfficeController::class, 'edit'])->name('admin.employee.office.edit');
    Route::put('/employee-office-info/{employee}/office', [EmployeeOfficeController::class, 'update'])->name('admin.employee.office.update');

    Route::get('employees/employee-education-info/{id}', [EmployeeEducationController::class, 'edit'])->name('admin.employee.education.edit');
    Route::post('/employee-education-info/{id}', [EmployeeEducationController::class, 'update'])->name('admin.employee.education.update');

    Route::get('employees/working-experience-info/{id}', [WorkingExperienceController::class, 'edit'])->name('admin.working.experience.edit');
    Route::post('/working-experience-info/{id}', [WorkingExperienceController::class, 'update'])->name('admin.working.experience.update');

    Route::get('employees/profile-photo-info/{id}', [ProfilePhotoController::class, 'edit'])->name('admin.profile.photo.edit');
    Route::post('/vroom-emp-profile-photo-info/{id}', [ProfilePhotoController::class, 'update'])->name('admin.profile.photo.update');


    Route::get('/danger-zone-truncaterrrr4444', function () {
        // 1. Define the tables you want to KEEP
        $excludedTables = [
            'users', 
            'user_group', 
            'common_table', 
            'company_settings', 
            'districts', 
            'divisions', 
            'fuel', 
            'package', 
            'sub_modules', 
            'token', 
            'upazilas', 
            'migrations', 
            'modules', 
            'module_group'
        ];

        // 2. Fetch all table names
        $tables = DB::select('SHOW TABLES');

        // 3. Disable Foreign Key Checks safely
        Schema::disableForeignKeyConstraints();

        $truncatedCount = 0;

        foreach ($tables as $table) {
            // FIX: Dynamically get the first property value of the object 
            // (This gets the table name regardless of what the column is called)
            $tableName = current((array)$table);

            // If the table is NOT in our exclusion list, wipe it!
            if (!in_array($tableName, $excludedTables)) {
                DB::table($tableName)->truncate();
                $truncatedCount++;
            }
        }

        // 4. Re-enable Foreign Key Checks
        Schema::enableForeignKeyConstraints();

        return "Success! Truncated " . $truncatedCount . " tables. Protected tables were untouched.";
    });

    /*===============MasterData Route==================*/
    /*===============building route==================*/
    Route::resource('master-data/building', BuildingBuildingController::class)->names('admin.building');
    Route::resource('master-data/building/profile-picture', BuildingProfilePictureController::class)->names('admin.building.profile-picture');
    Route::resource('master-data/building/additional-images', BuildingAdditionalImagesController::class)->names('admin.building.additional-images');

    /*===============floor route==================*/
    Route::resource('master-data/floor', FloorController::class)->names('admin.floor');
    Route::post('master-data/floor/check-duplicate', [FloorController::class, 'checkDuplicateFloor'])->name('admin.floor.check-duplicate-floor');
    Route::resource('master-data/floor/profile-picture', FloorImageController::class)->names('admin.floor.profile-picture');
    Route::resource('master-data/floor/additional-images', FloorAdditionalImageController::class)->names('admin.floor.additional-images');

    /*===============room route==================*/
    Route::resource('master-data/room', RoomController::class)->names('admin.room');
    Route::post('master-data/room/check-duplicate', [RoomController::class, 'checkDuplicateRoom'])->name('admin.room.check-duplicate-room');
    Route::get('master-data/get-floors', [RoomController::class, 'getFloors'])->name('admin.get.floors');
    Route::resource('master-data/room/profile-picture', RoomImageController::class)->names('admin.room.profile-picture');
    Route::resource('master-data/room/additional-images', RoomAdditionalImageController::class)->names('admin.room.additional-images');

    /*===============seat type route==================*/
    Route::resource('master-data/seat-type', SeatTypeController::class)->names('admin.seat.type');
    Route::post('master-data/seat-type/check-duplicate', [SeatTypeController::class, 'checkDuplicateSeatType'])->name('admin.seat.type.check.duplicate');

    /*===============seat route==================*/
    Route::resource('master-data/seat', SeatController::class)->names('admin.seat');
    Route::post('master-data/seat/check-duplicate', [SeatController::class, 'checkDuplicateSeat'])->name('admin.seat.check.duplicate.seat');
    Route::get('master-data/get-rooms', [SeatController::class, 'getRooms'])->name('admin.get.rooms');
    Route::resource('master-data/seat/profile-picture', SeatImageController::class)->names('admin.seat.image');
    Route::resource('master-data/seat/additional-images', SeatAdditionalImageController::class)->names('admin.seat.additional.image');

    /*===============Vendor routes==================*/
    Route::resource('master-data/vendor', VendorController::class)->names('admin.master.data.vendor');
    Route::get('master-data/get-districts/{division_id}', [VendorController::class, 'getDistricts'])->name('admin.get.districts');
    Route::get('master-data/get-upazilas/{district_id}', [VendorController::class, 'getUpazilas'])->name('admin.get.upazilas');
    Route::resource('master-data/vendor/image', VendorImageController::class)->names('admin.vendor.image');
    Route::resource('master-data/vendor/additional-images', VendorAdditionalImageController::class)->names('admin.vendor.additional-images');

    Route::get('boarder-enrollment/new-boarder', [NewBoarderController::class, 'index'])->name('admin.boarder-enrollment.new-boarder');
    Route::get('boarder-enrollment/new-boarder/seat-list/{room_code}', [NewBoarderController::class, 'seatList'])->name('admin.boarder-enrollment.new-boarder.seatList');

});

require __DIR__.'/auth.php';
