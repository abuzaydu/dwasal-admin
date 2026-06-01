<?php

use App\Http\Controller\FuelType;
use App\Http\Controllers\Acc\AccountingDashController;
use App\Http\Controllers\Acc\AccountsController;
use App\Http\Controllers\Acc\AccountTransController;
use App\Http\Controllers\Acc\BalanceSheetsController;
use App\Http\Controllers\Acc\CashInController;
use App\Http\Controllers\Acc\CashOutController;
use App\Http\Controllers\Acc\COAController;
use App\Http\Controllers\Acc\EmployeeLoanController;
use App\Http\Controllers\Acc\EmployeeLoanReturnController;
use App\Http\Controllers\Acc\ExpenseCategoryController;
use App\Http\Controllers\Acc\ExpenseController;
use App\Http\Controllers\Acc\ExpenseItemController;
use App\Http\Controllers\Acc\ExpensePaymentController;
use App\Http\Controllers\Acc\ExpenseTempController;
use App\Http\Controllers\Acc\ExpSupplierController;
use App\Http\Controllers\Acc\PettyCashController;
use App\Http\Controllers\Admin\BusinessTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PaymentAuthController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SenderIDController;
use App\Http\Controllers\Admin\ServiceChargeController;
use App\Http\Controllers\Admin\SmsAccountController;
use App\Http\Controllers\Admin\SubscriptionTypeController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserTransactionController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AppAPI\BadgeController;
use App\Http\Controllers\Asset\AssetRecordController;
use App\Http\Controllers\Asset\DepreciationController;
use App\Http\Controllers\Asset\DepreciationMethodController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AutoCompleteSearch;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HR\AcademicInfoController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\AttendanceSettingController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\EmployeeFaceIdController;
use App\Http\Controllers\HR\EmployeeDocController;
use App\Http\Controllers\HR\EmployeeMedicalInfoController;
use App\Http\Controllers\HR\EmployeeSalaryController;
use App\Http\Controllers\HR\EventController;
use App\Http\Controllers\HR\HolidayController;
use App\Http\Controllers\HR\HRDashController;
use App\Http\Controllers\HR\LeaveRosterController;
use App\Http\Controllers\HR\NextOfKinController;
use App\Http\Controllers\HR\NotificationController;
use App\Http\Controllers\HR\PositionController;
use App\Http\Controllers\Inventory\BrandController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\CorrectionTempController;
use App\Http\Controllers\Inventory\InventoryDashController;
use App\Http\Controllers\Inventory\ProdDamageController;
use App\Http\Controllers\Inventory\ProductsController;
use App\Http\Controllers\Inventory\ProductUnitController;
use App\Http\Controllers\Inventory\PurchaseCostItemController;
use App\Http\Controllers\Inventory\PurchaseCostItemTempController;
use App\Http\Controllers\Inventory\PurchaseOrderController;
use App\Http\Controllers\Inventory\PurchaseOrderItemController;
use App\Http\Controllers\Inventory\PurchaseOrderTempApiController;
use App\Http\Controllers\Inventory\PurchasePaymentController;
use App\Http\Controllers\Inventory\PurchasesController;
use App\Http\Controllers\Inventory\ShopProductsApiController;
use App\Http\Controllers\Inventory\StockController;
use App\Http\Controllers\Inventory\StockCorrectionController;
use App\Http\Controllers\Inventory\StockItemTempApiController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\TransferOrderController;
use App\Http\Controllers\Inventory\TransferOrderItemController;
use App\Http\Controllers\Inventory\TransferOrderItemTempController;
use App\Http\Controllers\Inventory\TransformationTransferItemController;
use App\Http\Controllers\Inventory\TransformationTransferItemTempController;
use App\Http\Controllers\MHC\AppointmentController;
use App\Http\Controllers\MHC\AppointmentProductController;
use App\Http\Controllers\MHC\AppointmentServiceController;
use App\Http\Controllers\MHC\DashController;
use App\Http\Controllers\MHC\DoctorController;
use App\Http\Controllers\MHC\MedicalHistoryController;
use App\Http\Controllers\MHC\PatientController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\PayrollDeductionController;
use App\Http\Controllers\Payroll\PayrollDeductionPaymentController;
use App\Http\Controllers\Payroll\PayrollSettingsController;
use App\Http\Controllers\Payroll\PayrollTempController;
use App\Http\Controllers\Payroll\PayrollToExpenseController;
use App\Http\Controllers\Prod\DLCItemController;
use App\Http\Controllers\Prod\DlcItemTempController;
use App\Http\Controllers\Prod\FoodProductionController;
use App\Http\Controllers\Prod\FoodProductionTempController;
use App\Http\Controllers\Prod\FoodTypeController;
use App\Http\Controllers\Prod\MaterialWIPsController;
use App\Http\Controllers\Prod\MaterialWIPTempController;
use App\Http\Controllers\Prod\MohCostController;
use App\Http\Controllers\Prod\MohCostItemController;
use App\Http\Controllers\Prod\MohCostPaymentController;
use App\Http\Controllers\Prod\MohCostTempController;
use App\Http\Controllers\Prod\MroApiController;
use App\Http\Controllers\Prod\MROController;
use App\Http\Controllers\Prod\MROItemController;
use App\Http\Controllers\Prod\MroUseController;
use App\Http\Controllers\Prod\MroUsedItemTempController;
use App\Http\Controllers\Prod\PackingMaterialApiController;
use App\Http\Controllers\Prod\PackingMaterialController;
use App\Http\Controllers\Prod\PC\ExportHandlingCostController;
use App\Http\Controllers\Prod\PC\IndirectCostController;
use App\Http\Controllers\Prod\PC\LabourCostController;
use App\Http\Controllers\Prod\PC\LocalIndirectCostController;
use App\Http\Controllers\Prod\PC\LocalPackagingCostController;
use App\Http\Controllers\Prod\PC\MaterialCostController;
use App\Http\Controllers\Prod\PC\PackagingCostController;
use App\Http\Controllers\Prod\PC\ProductPricingController;
use App\Http\Controllers\Prod\PC\TransportCostController;
use App\Http\Controllers\Prod\PlcPaymentController;
use App\Http\Controllers\Prod\PmDamageController;
use App\Http\Controllers\Prod\PmItemController;
use App\Http\Controllers\Prod\PmPurchaseController;
use App\Http\Controllers\Prod\PmPurchaseItemApiController;
use App\Http\Controllers\Prod\PmPurchasePaymentController;
use App\Http\Controllers\Prod\PmSupplierTransactionController;
use App\Http\Controllers\Prod\PmTransferController;
use App\Http\Controllers\Prod\PmTransferItemController;
use App\Http\Controllers\Prod\PmUseController;
use App\Http\Controllers\Prod\PmUseItemTempController;
use App\Http\Controllers\Prod\PPStageController;
use App\Http\Controllers\Prod\ProdCostItemController;
use App\Http\Controllers\Prod\ProdHomeController;
use App\Http\Controllers\Prod\ProdLabourCostController;
use App\Http\Controllers\Prod\ProdLabourCostItemController;
use App\Http\Controllers\Prod\ProdLabourCostTempController;
use App\Http\Controllers\Prod\ProdReportsController;
use App\Http\Controllers\Prod\ProdSettingController;
use App\Http\Controllers\Prod\ProdTransferController;
use App\Http\Controllers\Prod\ProductionApiController;
use App\Http\Controllers\Prod\ProductionCostController;
use App\Http\Controllers\Prod\ProductionStageController;
use App\Http\Controllers\Prod\RawMaterialApiController;
use App\Http\Controllers\Prod\RawMaterialController;
use App\Http\Controllers\Prod\RmDamageController;
use App\Http\Controllers\Prod\RmItemController;
use App\Http\Controllers\Prod\RmPurchaseController;
use App\Http\Controllers\Prod\RmPurchaseItemApiController;
use App\Http\Controllers\Prod\RmPurchasePaymentController;
use App\Http\Controllers\Prod\RmSupplierTransactionController;
use App\Http\Controllers\Prod\RmUseController;
use App\Http\Controllers\Prod\RmUsedItemController;
use App\Http\Controllers\Prod\RmUseItemTempController;
use App\Http\Controllers\Prod\WIPMaterialController;
use App\Http\Controllers\Prod\WIPsController;
use App\Http\Controllers\Prod\WIPTempController;
use App\Http\Controllers\Sales\AnSaleController;
use App\Http\Controllers\Sales\AnSaleItemController;
use App\Http\Controllers\Sales\CreditNoteController;
use App\Http\Controllers\Sales\CustomerCategoryController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\DeliveryAddressController;
use App\Http\Controllers\Sales\DeliveryNoteController;
use App\Http\Controllers\Sales\InvoiceController;
use App\Http\Controllers\Sales\InvoiceItemTempController;
use App\Http\Controllers\Sales\InvoiceNoteController;
use App\Http\Controllers\Sales\ProInvoiceController;
use App\Http\Controllers\Sales\RefundRequestController;
use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Sales\SaleDashController;
use App\Http\Controllers\Sales\SaleItemTempController;
use App\Http\Controllers\Sales\SaleOrderController;
use App\Http\Controllers\Sales\SaleOrderItemController;
use App\Http\Controllers\Sales\SalePaymentController;
use App\Http\Controllers\Sales\SaleReturnController;
use App\Http\Controllers\Sales\SaleReturnItemController;
use App\Http\Controllers\Sales\ServiceInvoiceItemTempController;
use App\Http\Controllers\Sales\ServiceSaleItemController;
use App\Http\Controllers\Sales\ServiceSaleItemTempController;
use App\Http\Controllers\SandProd\MaintenanceRecordController;
use App\Http\Controllers\SandProd\ProductionRunController;
use App\Http\Controllers\SandProd\QualityTestController;
use App\Http\Controllers\SandProd\RawMaterialSourceController;
use App\Http\Controllers\SandProd\RMSourcingController;
use App\Http\Controllers\SandProd\SandDashController;
use App\Http\Controllers\SandProd\StorageLocationController;
use App\Http\Controllers\SandProd\WashingEquipmentController;
use App\Http\Controllers\SandProd\WashingPlantController;
use App\Http\Controllers\Service\DeviceController;
use App\Http\Controllers\Service\GradeController;
use App\Http\Controllers\Service\ServCategoryController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Service\ShopServiceApiController;
use App\Http\Controllers\Settings\CompanyController;
use App\Http\Controllers\Settings\CompanyRoleController;
use App\Http\Controllers\Settings\DeliveryRateController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\ShopController;
use App\Http\Controllers\Settings\UnitEquivalentController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\OrderDeliveryController;
use App\Http\Controllers\Shop\OrderPaymentController;
use App\Http\Controllers\Shop\ProductImageController;
use App\Http\Controllers\Shop\QuotationController;
use App\Http\Controllers\Shop\QuoteRequestController;
use App\Http\Controllers\VFD\RctInfoController;
use App\Http\Controllers\VFD\RegInfoController;
use App\Http\Controllers\VFD\ZReportController;
use App\Http\Controllers\VML\VisitorController;
use App\Http\Controllers\VML\VisitorExportController;
use App\Http\Controllers\VMS\DocumentTypeController;
use App\Http\Controllers\VMS\DriverController;
use App\Http\Controllers\VMS\ExpenseAjaxController;
use App\Http\Controllers\VMS\ExpenseTypeController;
use App\Http\Controllers\VMS\FuelStation;
use App\Http\Controllers\VMS\FuelStationController;
use App\Http\Controllers\VMS\FuelTypeController;
use App\Http\Controllers\VMS\InsuranceCompanyController;
use App\Http\Controllers\VMS\InsuranceController;
use App\Http\Controllers\VMS\IrPeriodController;
use App\Http\Controllers\VMS\LegalDocumentController;
use App\Http\Controllers\VMS\LicenseTypeController;
use App\Http\Controllers\VMS\MaintenanceController;
use App\Http\Controllers\VMS\MaintenanceTypeController;
use App\Http\Controllers\VMS\OwnershipController;
use App\Http\Controllers\VMS\PartCategoryController;
use App\Http\Controllers\VMS\PartItemTempApiController;
use App\Http\Controllers\VMS\PartLocationController;
use App\Http\Controllers\VMS\PartPurchaseController;
use App\Http\Controllers\VMS\PartPurchaseItemController;
use App\Http\Controllers\VMS\PartsController;
use App\Http\Controllers\VMS\PartsUsageController;
use App\Http\Controllers\VMS\PartUsageItemController;
use App\Http\Controllers\VMS\RefuelingController;
use App\Http\Controllers\VMS\RequisitionPurposeController;
use App\Http\Controllers\VMS\RequisitionTripLogController;
use App\Http\Controllers\VMS\TripTypeController;
use App\Http\Controllers\VMS\VehicleController;
use App\Http\Controllers\VMS\VehicleRequisitionController;
use App\Http\Controllers\VMS\VehicleTypeController;
use App\Http\Controllers\VMS\VendorController;
use App\Http\Controllers\VMS\VmsExpenseController;
use App\Http\Controllers\Web\ActionLogsController;
use App\Http\Controllers\Web\ApprovalRequestController;
use App\Http\Controllers\Web\CompanyReportsController;
use App\Http\Controllers\Web\FinancialReportsController;
use App\Http\Controllers\Web\PurchaseReportController;
use App\Http\Controllers\Web\RecycleBinController;
use App\Http\Controllers\Web\ReportsController;
use App\Http\Controllers\Web\SmsTemplateController;
use App\Http\Controllers\Web\StockReportController;
use App\Http\Controllers\Web\TripLogsController;
use App\Http\Controllers\Web\VerifyPaymentController;
use App\Http\Controllers\WelcomeController;
use App\Models\Driver;
use App\Models\RequisitionTripLog;
use App\Models\User;
use App\Notifications\FcmNotification;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('remove-zero-stock', [WelcomeController::class, 'removeZeroStock']);
Route::get('/', [WelcomeController::class, 'index']);
Route::get('privacy', [WelcomeController::class, 'privacy']);
Route::get('terms', [WelcomeController::class, 'terms']);
Route::get('test-print', [WelcomeController::class, 'testPrint']);
Route::get('drop-tables', [WelcomeController::class, 'dropTables']);
Route::get('check-migrations', [WelcomeController::class, 'checkMigrations']);
Route::get('my-default-page', [WelcomeController::class, 'defaultPage']);
Route::get('verify-invoice/{id}', [WelcomeController::class, 'verifyInvoice']);
Auth::routes();
Route::post('get-sub-types', [RegisterController::class, 'getBSubTypes']);
Route::post('password-phone', [ForgotPasswordController::class, 'forgotPass']);
Route::post('verify-code', [ResetPasswordController::class, 'verifyCode']);

Route::post('reset-pass', [ResetPasswordController::class, 'resetPass']);

//Admin Routes
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::get('admin-dash', [DashboardController::class, 'index'])->name('home');
    Route::get('totals', [DashboardController::class, 'total']);
    Route::get('new-activations', [DashboardController::class, 'newActivations']);
    Route::resource('service-charges', ServiceChargeController::class);
    Route::resource('sms-templates', SmsTemplateController::class);
    Route::resource('modules', ModuleController::class);
    Route::resource('payments', PaymentController::class);
    Route::post('f-service-payments', [PaymentController::class, 'index']);
    Route::get('activate-shop', [PaymentController::class, 'activateShopForm']);
    Route::post('activate-shop', [PaymentController::class, 'activateShop']);
    Route::get('payments-export/{from}/{to}/{term}/{id}', [PaymentController::class, 'paymentsExport']);
    Route::get('/payments-search-query', [PaymentController::class, 'query'])->name('payments.search.query');
    Route::get('activated-payments', [PaymentController::class, 'activatedPayments']);
    Route::post('activated-payments', [PaymentController::class, 'activatedPayments']);
    Route::get('agent-activations', [PaymentController::class, 'agentActivations']);
    Route::get('activations-once', [PaymentController::class, 'activationsOnce']);
    Route::post('activations-once', [PaymentController::class, 'activationsOnce']);
    Route::post('post-users', [UserController::class, 'index']);
    Route::resource('users', UserController::class);
    Route::get('export-users', [UserController::class, 'exportUsers']);
    Route::get('reset-password', [UserController::class, 'passwordResets']);
    Route::get('staffs', [UserController::class, 'staffs']);
    Route::get('users-destroy/{id}', [UserController::class, 'destroy']);
    Route::get('clear-reset-codes', [UserController::class, 'clearResetCodes']);
    Route::post('registered-users', [UserController::class, 'index']);
    Route::post('new-role', [UserController::class, 'newRole']);
    Route::get('active-users', [UserController::class, 'activeUsers']);
    Route::get('guest-users', [UserController::class, 'guestUsers']);
    Route::get('shops', [UserController::class, 'shops']);
    Route::get('update-shop-detail/{id}', [UserController::class, 'editShop']);
    Route::post('update-shop-detail', [UserController::class, 'updateShop']);
    Route::get('export-shops', [UserController::class, 'exportShops']);
    Route::post('shops', [UserController::class, 'shops']);
    Route::get('change-subscr-type/{id}', [UserController::class, 'changeSubscriptionType']);
    Route::get('active-shops', [UserController::class, 'activeShops']);
    Route::resource('roles', RoleController::class);
    Route::get('modify-roles', [RoleController::class, 'modify']);
    Route::get('roles/destroy/{id}', [RoleController::class, 'destroy']);
    Route::post('assign-role', [UserController::class, 'assignUserRole']);
    Route::post('detach-role', [UserController::class, 'detachUserRole']);
    Route::post('create-agent-code', [UserController::class, 'createAgentCode']);
    Route::get('customers-by-agents', [UserController::class, 'agentsCustomers']);
    Route::resource('permissions', PermissionController::class);
    Route::post('f-permissions', [PermissionController::class, 'index']);
    Route::get('permissions/destroy/{id}', [PermissionController::class, 'destroy']);
    Route::get('user-shops', [UserTransactionController::class, 'shops']);
    Route::get('act-shops', [UserTransactionController::class, 'getShops']);
    Route::get('sh-sales', [Admin\UserTransactionController::class, 'sales']);
    Route::get('sales', [UserTransactionController::class, 'getSales']);
    Route::get('sh-items', [UserTransactionController::class, 'items']);
    Route::get('items', [UserTransactionController::class, 'getItems']);
    Route::get('sh-products', [UserTransactionController::class, 'products']);
    Route::get('products', [UserTransactionController::class, 'getProducts']);
    Route::get('sh-stocks', [UserTransactionController::class, 'stocks']);
    Route::get('stocks', [UserTransactionController::class, 'getStocks']);
    Route::get('item/{id}', [UserTransactionController::class, 'getItem']);
    Route::post('update-item', [UserTransactionController::class, 'updateItem']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::get('change-password', [UserController::class, 'changePassForm']);
    Route::post('change-password', [UserController::class, 'changePass']);
    Route::post('change-theme', [UserController::class, 'changeTheme']);
    Route::resource('types', BusinessTypeController::class);
    Route::resource('subscriptions', SubscriptionTypeController::class);
    Route::get('types/destroy/{id}', [BusinessTypeController::class, 'destroy']);
    Route::get('subscriptions/destroy/{id}', [SubscriptionTypeController::class, 'destroy']);
    Route::resource('sms-accounts', SmsAccountController::class);
    // Route::get('sms-logs', [SmsAccountController::class, 'smsResponseLogs']);
    // Route::get('clear-logs', [SmsAccountController::class, 'clearSMSLogs']);
    Route::resource('sender-ids', SenderIDController::class);
    Route::get('sender-ids/destroy/{id}', [SenderIDController::class, 'destroy']);

    Route::resource('payment-auths', PaymentAuthController::class);
    Route::get('create-new-token/{id}', [PaymentAuthController::class, 'createNewToken']);
    Route::get('generate-key', [PaymentAuthController::class, 'generateKey']);
});

Route::group(['middleware' => 'auth'], function () {
//vml routes
    Route::get('/badges',[BadgeController::class, 'index'])->name('badges.index');
    Route::get('badge/print-one-badge', [BadgeController::class, 'autoPrintFOrOneBadge'])
    ->name('badges.auto.print-one-badge');
    Route::get('badge/{id}',[BadgeController::class, 'show'])->name('badges.show');
    Route::post('/badges/bulk',      [BadgeController::class, 'storeBulk'])->name('badges.storeBulk');
    Route::delete('/{id}',    [BadgeController::class, 'destroy'])->name('badges.destroy');
    Route::get('badges/auto-print', [BadgeController::class, 'autoPrint'])
    ->name('badges.auto.print');
    Route::get('badges/print-selected-badge', [BadgeController::class, 'printSelected'])
    ->name('badges.print.selected-badge');
    Route::get('badge/print-one-badge', [BadgeController::class, 'autoPrintFOrOneBadge'])
    ->name('badges.auto.print-one-badge');
}

//Vehicle management

);
//SmartMauzo customers routes
Route::group(['middleware' => 'auth'], function () {

    Route::get('home', [HomeController::class, 'index']);

    Route::resource('delivery-rates', DeliveryRateController::class);
    Route::resource('unit-equivalents', UnitEquivalentController::class);

    Route::get('signup-complete', [HomeController::class, 'completeSignupForm']);
    Route::post('complete-signup', [HomeController::class, 'completeSignup']);
    Route::get('close-shop', [HomeController::class, 'closeShop']);
    Route::get('verify-payment', [VerifyPaymentController::class, 'index'])->name('verify-payment');
    Route::post('verify-payment', [VerifyPaymentController::class, 'verify']);
    Route::get('verify-module-payment/{id}', [VerifyPaymentController::class, 'modulePayment']);
    Route::post('verify-module-payment', [VerifyPaymentController::class, 'verifyModulePayment']);
    Route::get('make-payment', [VerifyPaymentController::class, 'makePayment']);
    Route::resource('payment-transactions', TransactionController::class);
    Route::post('card-payment-transactions', [TransactionController::class, 'initiateOrder']);
    Route::post('check-payment-order-status', [TransactionController::class, 'checkOrderStatus']);

    Route::post('pesapal-iframe', [PesapalTransactionController::class, 'store']);
    // Route::get('pesapal-update', [PesaPalPaymentsController::class, 'update']);
    Route::get('pesapal-ipn', [PesapalTransactionController::class, 'paymentConfirmation']);
    Route::get('donepayment', ['as' => 'paymentsuccess', 'uses' => [PesapalTransactionController::class, 'paymentsuccess']]);

    Route::get('view-receipt/{id}', [ProfileController::class, 'viewReceipt']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);
    Route::get('change-password', [ProfileController::class, 'changePassForm']);
    Route::post('change-password', [ProfileController::class, 'changePass']);
    Route::get('reset-password/{id}', [ProfileController::class, 'resetUserPassForm']);
    Route::post('reset-password', [ProfileController::class, 'resetUserPass']);
    Route::post('change-theme', [ProfileController::class, 'changeTheme']);
    Route::resource('shops', ShopController::class);
    Route::get('add-store', [ShopController::class, 'addStore']);
    Route::post('add-warehouse', [ShopController::class, 'postStore']);
    Route::resource('bank-details', BankDetailController::class);
    Route::get('delete-bank/{id}', [BankDetailController::class, 'destroy']);
    Route::post('switch-shop', [ShopController::class, 'switchShop']);
    Route::get('notifications', [ShopController::class, 'notifications']);
    Route::get('all-notifications', [ShopController::class, 'notifications']);
    Route::get('mark-as-read', [ShopController::class, 'markAsRead']);
    // Users Management
    Route::resource('user-profile', ProfileController::class);
    Route::post('add-user-from-employee', [ProfileController::class, 'createUser']);
    Route::get('users-and-roles', [ProfileController::class, 'usersAndRoles']);
    Route::post('users-and-roles', [ProfileController::class, 'usersAndRoles']);
    Route::resource('user-companies', CompanyController::class);
    Route::post('switch-company', [CompanyController::class, 'switchCompany']);
    Route::resource('company-roles', CompanyRoleController::class);
    Route::post('assign-business', [ProfileController::class, 'assignBusiness']);
    Route::post('detach-business', [ProfileController::class, 'detachBusiness']);
    Route::get('activate-user/{id}', [ProfileController::class, 'activateUser']);
    Route::get('remove-user/{id}', [ProfileController::class, 'removeUser']);
    Route::post('change-user-role', [ProfileController::class, 'assignUserRole']);
    Route::post('add-permission', [ProfileController::class, 'assignPermissions']);
    Route::post('remove-permission', [ProfileController::class, 'removePermissions']);
    Route::get('revoke-all-permissions-from-user/{id}', [ProfileController::class, 'revokeAll']);
    Route::get('delete-user/{id}', [ProfileController::class, 'destroy']);
    Route::get('upgrade', [SettingsController::class, 'upgrade']);
    Route::get('downgrade', [SettingsController::class, 'downgrade']);
    Route::post('update-bsettings', [SettingsController::class, 'store']);
    Route::resource('settings', SettingsController::class);
    Route::get('invoice-template-settings', [SettingsController::class, 'invoiceTemplateSetting']);
    Route::get('daily-closing-time-settings', [SettingsController::class, 'dctSettings']);
    Route::post('edit-settings', [SettingsController::class, 'update']);
    Route::post('change-btype', [SettingsController::class, 'show']);
    Route::post('set-currency', [SettingsController::class, 'setCurrency']);
    Route::get('rem-currency/{id}', [SettingsController::class, 'removeCurrency']);
    Route::get('make-default-currency/{id}', [SettingsController::class, 'makeDefaultCurrency']);

    Route::resource('prod-settings', ProdSettingController::class);


    //Sales Managment routes
    Route::get('sales-dash', [SaleDashController::class, 'index']);
    Route::post('sales-dash', [SaleDashController::class, 'index']);
    Route::resource('sale-orders', SaleOrderController::class);
    Route::post('f-sale-orders', [SaleOrderController::class, 'index']);
    Route::post('sale-orders/update-so', [SaleOrderController::class, 'updateSO']);
    Route::post('sale-orders/pt-so', [SaleOrderController::class, 'editSO']);
    Route::get('sale-orders/api/usebarcode', [ShopProductsApiController::class, 'useBarcode']);
    Route::get('sale-orders/api/so-items/{id}', [SaleOrderItemController::class, 'index']);
    Route::resource('sale-orders/api/so-items', SaleOrderItemController::class);
    Route::get('approve-so/{id}', [SaleOrderController::class, 'approveSO']);
    Route::post('reject-so', [SaleOrderController::class, 'rejectSO']);
    Route::post('sale-orders/so-packed', [SaleOrderController::class, 'editPacked']);
    Route::post('confirm-packaged', [SaleOrderController::class, 'confirmPackaged']);
    Route::post('create-sale-from-so', [SaleOrderController::class, 'createSale']);
    Route::resource('pos', SaleController::class);
    Route::get('close', [SaleController::class, 'getSuccess']);
    Route::post('pos-temp', [SaleController::class, 'postTempData']);
    Route::post('pt-pos', [SaleController::class, 'edit']);
    Route::post('reset-pos-temp', [SaleController::class, 'resetTempData']);
    Route::get('sale-info/{id}', [SaleController::class, 'show']);
    Route::resource('an-sales', AnSaleController::class);
    Route::post('an-sales', [AnSaleController::class,  'index']);
    Route::get('issue-vfd/{id}', [AnSaleController::class, 'issueVFD']);
    Route::get('print-receipt/{id}', [AnSaleController::class, 'printReceipt']);
    Route::post('delete-multiple-sales', [AnSaleController::class, 'deleteMultiple']);
    Route::post('api/add-customer-to-session', [SaleItemTempController::class, 'selectedCustomer']);
    //SaleItems temp Routes
    Route::resource('api/item', ShopProductsApiController::class);
    Route::get('search-product', [ShopProductsApiController::class, 'autoSearch']);
    Route::get('fetch-product', [ShopProductsApiController::class, 'fetchProduct']);
    Route::get('fetch-by-barcode', [ShopProductsApiController::class, 'store']);
    Route::get('api/usebarcode', [ShopProductsApiController::class, 'useBarcode']);
    Route::post('api/add-item', [SaleItemTempController::class, 'ajaxPost']);
    Route::get('api/saletemp/{id}', [SaleItemTempController::class, 'index']);
    Route::resource('api/saletemp', SaleItemTempController::class);
    Route::post('api/update-sale-mode', [SaleItemTempController::class, 'updateSaleMode']);
    Route::resource('sale-items', AnSaleItemController::class);
    Route::post('add-saleitem', [AnSaleItemController::class, 'create']);
    Route::post('add-serviceitem', [AnSaleItemController::class, 'addItem']);
    Route::get('edit-item/{id}', [AnSaleItemController::class, 'edit']);
    Route::get('edit-sale-item/{id}', [AnSaleItemController::class, 'editItem']);
    Route::post('update-item', [AnSaleItemController::class, 'update']);
    Route::get('delete-item/{id}', [AnSaleItemController::class, 'destroy']);
    //Saleitems Temp Routes End
    //ServiceSaleItems temp Routes
    Route::resource('api/servitem', ShopServiceApiController::class);
    Route::get('search-service', [ShopServiceApiController::class, 'autoSearch']);
    Route::get('api/servsaletemp/{id}', [ServiceSaleItemTempController::class, 'index']);
    Route::resource('api/servsaletemp', ServiceSaleItemTempController::class);
    Route::resource('service-items', ServiceSaleItemController::class);
    Route::get('delete-serviceitem/{id}', [ServiceSaleItemController::class, 'destroy']);
    //ServiceSaleitems Temp Routes End
    Route::resource('invoice-notes', InvoiceNoteController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::post('f-invoices', [InvoiceController::class,  'index']);
    Route::get('print-order', [InvoiceController::class, 'printOrder']);
    Route::get('print-invoice', [InvoiceController::class, 'printInvoice']);
    Route::post('delete-multiple-invoices', [InvoiceController::class, 'deleteMultiple']);
    Route::get('customer-accounts', [InvoiceController::class, 'customerAccounts']);
    Route::get('customer-account-stmt/{id}', [InvoiceController::class, 'accountStmt']);
    Route::post('customer-account-stmt/{id}', [InvoiceController::class, 'accountStmt']);
    Route::post('acc-payments', [InvoiceController::class, 'accPayments']);
    Route::post('set-ob', [InvoiceController::class, 'setOpeningBalance']);
    Route::get('del-acc-payment/{receipt_no}', [InvoiceController::class, 'deletePayment']);
    Route::get('del-acc-inv/{id}', [InvoiceController::class, 'deleteTrans']);
    Route::get('show-receipt/{id}', [InvoiceController::class, 'showReceipt']);
    Route::get('edit-acc-payment/{id}', [InvoiceController::class, 'editPaymentTrans']);
    Route::post('update-acc-payment', [InvoiceController::class, 'updatePaymentTrans']);
    Route::post('change-discount', [InvoiceController::class, 'changeDiscount']);
    Route::get('create-an-invoice/{id}', [InvoiceController::class, 'create']);
    Route::get('delete-invoice/{id}', [InvoiceController::class, 'destroy']);
    Route::get('create-credit-note/{id}', [CreditNoteController::class, 'create']);
    Route::get('create-sale-return/{id}', [SaleReturnController::class, 'create']);
    Route::resource('sales-returns', SaleReturnController::class);
    Route::post('sale-returns', [SaleReturnController::class, 'index']);
    Route::get('delete-sale-return/{id}', [SaleReturnController::class, 'destroy']);
    Route::resource('sale-return-items', SaleReturnItemController::class);
    Route::post('update-sale-return-item', [SaleReturnItemController::class, 'update']);
    Route::get('delete-sale-return-item/{id}', [SaleReturnItemController::class, 'destroy']);
    Route::resource('refund-requests', RefundRequestController::class);
    Route::get('refund-requests/approve-refund/{id}', [RefundRequestController::class, 'approveRefund']);
    Route::post('reject-refund', [RefundRequestController::class, 'rejectRefund']);
    Route::resource('credit-notes', CreditNoteController::class);
    Route::get('cancel-credit-note/{id}', [CreditNoteController::class, 'destroy']);
    Route::resource('credit-note-items', CreditNoteItemController::class);
    Route::get('credit-note-items/destroy/{id}', [CreditNoteItemController::class, 'destroy']);
    Route::resource('pro-invoices', ProInvoiceController::class);
    Route::post('f-pro-invoices', [ProInvoiceController::class, 'index']);
    Route::get('pro-invoices/destroy/{id}', [ProInvoiceController::class, 'destroy']);
    Route::get('invoice-items/{id}', [ProInvoiceController::class, 'proinvoiceItems']);
    Route::get('cancel-invoice', [ProInvoiceController::class, 'cancel']);
    Route::post('create-invoice', [ProInvoiceController::class, 'createSale']);
    Route::post('update-customer', [ProInvoiceController::class, 'updateCustomer']);
    Route::post('change-customer', [ProInvoiceController::class, 'changeCustomer']);
    Route::post('add-invoice-item', [ProInvoiceController::class, 'addItem']);
    Route::post('add-invocie-servitem', [ProInvoiceController::class, 'addServiceItem']);
    Route::get('edit-invoice-item/{id}', [ProInvoiceController::class, 'editInvoiceItem']);
    Route::get('edit-invoice-servitem/{id}', [ProInvoiceController::class, 'editInvoiceServItem']);
    Route::post('update-invoice-item', [ProInvoiceController::class, 'updateInvoiceItem']);
    Route::post('update-invoice-servitem', [ProInvoiceController::class, 'updateInvoiceServiceItem']);
    Route::get('delete-invoice-item/{id}', [ProInvoiceController::class, 'deleteItem']);
    Route::get('delete-invoice-servitem/{id}', [ProInvoiceController::class, 'deleteServiceItem']);
    Route::post('delete-multiple-pro-invoices', [ProInvoiceController::class, 'deleteMultiple']);
    Route::get('cancel-profoma/{id}', [ProInvoiceController::class, 'cancelProfoma']);
    Route::get('resume-profoma/{id}', [ProInvoiceController::class, 'resumeProfoma']);
    Route::get('invoice-payments/{id}', [InvoicePaymentController::class, 'index']);
    Route::resource('inv-payments', InvoicePaymentController::class);
    Route::get('inv-payments/destroy/{id}', [InvoicePaymentController::class, 'destroy']);
    //ProInvoice Items temp Routes
    Route::resource('api/invoiceitem', ShopProductsApiController::class);
    Route::get('add-invoiceitem-by-barcode', [InvoiceItemTempController::class, 'ajaxPost']);
    Route::resource('api/invoicetemp', InvoiceItemTempController::class);
    Route::post('cp-orders', [ProInvoiceController::class, 'cpOrders']);
    Route::get('pending-orders/{id}', [ProInvoiceController::class, 'pendingOrders']);
    //ServiceInvoiceItems temp Routes
    Route::resource('api/invoice-servitem', ShopServiceApiController::class);
    Route::resource('api/servinvoicetemp', ServiceInvoiceItemTempController::class);
    Route::get('invoice-report', [InvoiceController::class, 'invoiceReport']);
    Route::post('invoice-reports', [InvoiceController::class, 'invoiceReport']);
    Route::get('aging-report', [InvoiceController::class, 'agingReport']);

    //Delivery Note
    Route::resource('delivery-addresses', DeliveryAddressController::class);
    Route::get('create-dnote/{id}', [DeliveryNoteController::class, 'create']);
    Route::get('create-dnote-pfi/{id}', [DeliveryNoteController::class, 'createFromPFI']);
    Route::post('dnote-from-pfi', [DeliveryNoteController::class, 'postDNote']);
    Route::resource('delivery-notes', DeliveryNoteController::class);
    Route::post('f-delivery-notes', [DeliveryNoteController::class, 'index']);
    Route::post('update-delivery-note-item', [DeliveryNoteController::class, 'updateDNoteItem']);
    Route::get('remove-delivery-note-item/{id}', [DeliveryNoteController::class, 'removeDNoteItem']);
    Route::get('customer-account-stmt-std/{id}', [AnSaleController::class, 'accountStmt']);
    Route::post('customer-account-stmt-std/{id}', [AnSaleController::class, 'accountStmt']);
    Route::post('set-cust-ob', [AnSaleController::class, 'setOpeningBalance']);
    Route::post('update-cust-ob', [AnSaleController::class, 'updateOB']);
    Route::get('del-custacc-payment/{receipt_no}', [AnSaleController::class, 'deletePayment']);
    Route::get('show-cust-receipt/{receipt_no}', [AnSaleController::class, 'showReceipt']);
    Route::post('sale-acc-payments', [AnSaleController::class, 'accPayments']);
    Route::post('f-sale-payments', [SalePaymentController::class, 'index']);
    Route::resource('sale-payments', SalePaymentController::class);
    Route::get('sale-payments/destroy/{id}', [SalePaymentController::class, 'destroy']);
    Route::get('total-sale-payments', [SalePaymentController::class, 'totalSalePayments']);
    Route::post('f-total-sale-payments', [SalePaymentController::class, 'totalSalePayments']);

    Route::get('api-pay-transactions', [SalePaymentController::class, 'airtelTransactions']);
    Route::post('api-pay-transactions', [SalePaymentController::class, 'airtelTransactions']);

    Route::resource('approval-requests', ApprovalRequestController::class);
    Route::get('approve-discount/{id}', [ApprovalRequestController::class, 'create']);
    Route::get('mark-all-as-read', [ApprovalRequestController::class, 'markAsRead']);
    Route::get('approve-pro-invoice/{id}', [ApprovalRequestController::class, 'approveInvoice']);
    Route::get('reject-pro-invoice/{id}', [ApprovalRequestController::class, 'rejectProformaInvoice']);
    //Sales routes End

    // INVENTORY MANAGEMENT
    Route::get('inventory-dash', [InventoryDashController::class, 'index']);
    Route::post('inventory-dash', [InventoryDashController::class, 'index']);
    //Product Categories Routes
    Route::resource('categories', CategoryController::class);
    Route::get('categories/destroy/{id}', [CategoryController::class, 'destroy']);
    Route::post('delete-multiple-categories', [CategoryController::class, 'deleteMultiple']);
    Route::get('category-products/{id}', [CategoryController::class, 'categoryProducts']);
    Route::post('add-product', [CategoryController::class, 'addProductToCategory']);
    Route::post('remove-product', [CategoryController::class, 'removeProductFromCategory']);
    Route::get('remove-all-prods-from-category/{id}', [CategoryController::class, 'removeAll']);

    //Service Categories Routes
    Route::resource('serv-categories', ServCategoryController::class);
    Route::get('serv-categories/destroy/{id}', [ServCategoryController::class, 'destroy']);
    Route::post('delete-multiple-serv-categories', [ServCategoryController::class, 'deleteMultiple']);
    Route::get('category-services/{id}', [ServCategoryController::class, 'categoryServices']);
    Route::post('add-service', [ServCategoryController::class, 'addServiceToCategory']);
    Route::post('remove-service', [ServCategoryController::class, 'removeServiceFromCategory']);
    Route::get('remove-all-serv-from-category/{id}', [ServCategoryController::class, 'removeAll']);

    //Inventory Products Routes
    Route::resource('brands', BrandController::class);
    Route::post('filter-products', [ProductsController::class, 'index']);
    Route::resource('products', ProductsController::class);
    Route::get('activate-deactivate-product/{id}', [ProductsController::class, 'ActivateDeactivateProduct']);
    Route::get('generate-barcodes', [ProductsController::class, 'generateBarcode']);
    Route::get('deactivated-products', [ProductsController::class, 'inActiveProducts']);
    Route::post('check-product-no', [ProductsController::class, 'checkProductNo']);
    Route::get('product-sale-history/{id}', [ProductsController::class, 'productSalesHistory']);
    Route::post('product-sale-history/{id}', [ProductsController::class, 'productSalesHistory']);
    Route::post('products/getShopProducts', [ProductsController::class, 'getShopProducts'])->name('products.getShopProducts');
    Route::get('products-with-vat', [ProductsController::class, 'productsWithVAT']);
    Route::get('products-without-vat', [ProductsController::class, 'productsWithoutVAT']);
    Route::resource('product-units', ProductUnitController::class);
    Route::get('create-basic-unit/{id}', [ProductUnitController::class, 'addBasicUnit']);
    Route::get('new-product', [ProductsController::class, 'create']);
    Route::get('excel-sample', [ProductsController::class, 'download']);
    Route::post('import-product', [ProductsController::class, 'import']);
    Route::get('export-product', [ProductsController::class, 'export']);
    Route::post('delete-multiple-products', [ProductsController::class, 'deleteMultiple']);
    Route::get('product-details/{id}', [ProductsController::class, 'view']);
    Route::get('set-actual-prices/{id}', [ProductsController::class, 'setActualPrices']);
    Route::get('new-price/{id}', [ProductsController::class, 'newPrice']);
    Route::post('new-sell-price', [ProductsController::class, 'postPrice']);
    Route::post('new-buy-price', [ProductsController::class, 'newBuyPrice']);
    Route::post('new-reorder-point', [ProductsController::class, 'newReorderPoint']);
    Route::post('new-location', [ProductsController::class, 'changeLocation']);
    Route::get('price-list', [ProductsController::class, 'priceList']);
    Route::post('create-st-request', [TransferOrderController::class, 'createStockTransferOrder']);
    Route::post('pt-sto-request', [TransferOrderController::class, 'createStockTransferOrder']);
    Route::resource('transfer-orders', TransferOrderController::class);
    Route::post('pt-sto', [TransferOrderController::class, 'create']);
    Route::post('f-transfer-orders', [TransferOrderController::class, 'index']);
    Route::get('sto-value/{id}', [TransferOrderController::class, 'stoValue']);
    Route::get('print-sto', [TransferOrderController::class, 'printOrder']);
    Route::get('cancel-temp-order/{id}', [TransferOrderController::class, 'cancelOrder']);
    Route::get('cancel-sto/{id}', [TransferOrderController::class, 'cancelSTO']);
    Route::get('delete-order/{id}', [TransferOrderController::class, 'destroy']);
    Route::get('api/orderitemtemp/{id}', [TransferOrderItemTempController::class, 'index']);
    Route::post('api/update-order-temp', [TransferOrderItemTempController::class, 'updateOrderTemp']);
    Route::resource('api/orderitemtemp', TransferOrderItemTempController::class);
    Route::post('update-transorder-item', [TransferOrderController::class, 'updateTransorderItem']);
    Route::get('receive-sto-transfer/{id}', [TransferOrderController::class, 'receiveTransfer']);
    Route::post('update-received-item', [TransferOrderController::class, 'updateReceivedItem']);
    Route::post('confirm-receive-transfer', [TransferOrderController::class, 'confirmReceived']);
    Route::get('modify-received-sto/{id}', [TransferOrderController::class, 'modifyReceivedSTO']);
    Route::post('add-order-item', [TransferOrderItemController::class, 'store']);
    Route::post('update-request-qty', [TransferOrderItemController::class, 'update']);
    Route::get('remove-item/{id}', [TransferOrderItemController::class, 'destroy']);
    Route::get('delete-transorder-item/{id}', [TransferOrderController::class, 'deleteTransorderItem']);
    Route::resource('transformation-transfer', TransformationTransferItemController::class);
    Route::resource('transformation-transfer-temp', TransformationTransferItemTempController::class);
    Route::post('api/destin_produts', [TransformationTransferItemTempController::class, 'destinProducts']);
    Route::get('transfer-to-item', [TransferOrderController::class, 'transferToItem']);
    Route::post('transfer-item-to-item', [TransferOrderController::class, 'transferItems']);
    Route::get('mix-items', [TransferOrderController::class, 'mixItems']);
    Route::get('add-mix-item', [TransferOrderController::class, 'addMixItem']);
    Route::post('update-mix-item', [TransferOrderController::class, 'updateMixItem']);
    Route::get('remove-mix-item/{id}', [TransferOrderController::class, 'removeMixItem']);
    Route::post('transfer-mix-items', [TransferOrderController::class, 'transferMixitems']);
    Route::get('add-sto-mix-item', [TransferOrderController::class, 'addSTOMixItem']);
    Route::post('update-sto-mix-item', [TransferOrderController::class, 'updateSTOMixItem']);
    Route::get('remove-sto-mix-item/{id}', [TransferOrderController::class, 'removeSTOMixItem']);
    Route::post('update-transfer-mix-items', [TransferOrderController::class, 'updateTransferMixitems']);

    Route::get('transfer-to-rm', [TransferOrderController::class, 'transferToRM']);
    Route::post('transfer-item-to-rm', [TransferOrderController::class, 'transferItemtoRM']);
    Route::get('edit-item-to-rm/{id}', [TransferOrderController::class, 'editTransferToRM']);
    Route::post('update-transfer-item-to-rm', [TransferOrderController::class, 'updateTransferItemtoRM']);

    Route::resource('stock-corrections', StockCorrectionController::class);
    Route::post('f-stock-corrections', [StockCorrectionController::class, 'index']);
    Route::resource('stock-corrections/api/correction-temp', CorrectionTempController::class);
    Route::get('cancel-correction', [StockCorrectionController::class, 'cancel']);
    Route::post('delete-multiple-corrections', [StockCorrectionController::class, 'deleteMultiple']);
    //Products Routes end

    //Supplier and Purchases Routes
    //Suppliers
    Route::resource('suppliers', SupplierController::class);
    Route::get('sample-suppliers', [SupplierController::class, 'downloadSample']);
    Route::post('import-suppliers', [SupplierController::class, 'import']);
    Route::post('supplier-account-stmt/{id}', [SupplierController::class, 'show']);
    Route::get('edit-supplier/{id}', [SupplierController::class, 'edit']);
    Route::post('update-supplier', [SupplierController::class, 'update']);
    Route::get('delete-supplier/{id}', [SupplierController::class, 'destroy']);
    // Purchase Orders
    Route::post('pt-porders', [PurchaseOrderController::class, 'pendingOrders']);
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('approve-po/{id}', [PurchaseOrderController::class, 'approvePO']);
    // Route::get('purchase-excel-sample', [PurchaseOrderController::class, 'downloadSample']);
    Route::get('cancel-porder', [PurchaseOrderController::class, 'cancelPorder']);
    Route::post('delete-multiple-porders', [PurchaseOrderController::class, 'deleteMultiple']);
    Route::resource('purchase-orders/api/poitem', ShopProductsApiController::class);
    Route::get('purchase-orders/api/usebarcode', [ShopProductsApiController::class, 'useBarcode']);
    Route::get('api/pordertemp/{id}', [PurchaseOrderTempApiController::class, 'index']);
    Route::post('purchase-order-imports', [PurchaseOrderTempApiController::class, 'importItems']);
    Route::resource('api/pordertemp', PurchaseOrderTempApiController::class);
    Route::post('api/add-poitem-temp', [PurchaseOrderTempApiController::class, 'ajaxPost']);
    Route::post('api/update-po-temp', [PurchaseOrderTempApiController::class, 'updateOrderTempInfo']);
    Route::get('poitems/{id}', [PurchaseOrderItemController::class, 'index']);
    Route::resource('poitems', PurchaseOrderItemController::class);
    Route::post('add-purchase-order-item', [PurchaseOrderItemController::class, 'create']);
    Route::post('purchases/create-purchase', [PurchaseOrderController::class, 'createPurchase']);
    Route::get('purchases/api/usebarcode', [ShopProductsApiController::class, 'useBarcode']);

    //Inventory Purchases
    Route::resource('purchases/api/pitem', ShopProductsApiController::class);
    Route::get('purchases/api/stocktemp/{id}', [StockItemTempApiController::class, 'index']);
    Route::resource('purchases/api/stocktemp', StockItemTempApiController::class);
    Route::resource('purchases/api/costtemp', PurchaseCostItemTempController::class);
    Route::get('grn-fetch-by-barcode', [StockItemTempApiController::class, 'ajaxPost']);
    Route::post('purchases/api/update-purchase-temp', [StockItemTempApiController::class, 'updatePurchaseTemp']);
    Route::post('purchases/pt-purchase', [PurchasesController::class, 'pendingPurchase'])->name('purchases/pt-purchase');
    Route::get('cancel-purchase/{id}', [StockItemTempApiController::class, 'cancelPurchase']);
    Route::get('cancel-production/{id}', [StockItemTempApiController::class, 'cancelProduction']);
    Route::post('f-purchases', [PurchasesController::class, 'index']);
    Route::resource('purchases', PurchasesController::class);
    Route::get('productions', [PurchasesController::class, 'index1']);
    Route::post('f-productions', [PurchasesController::class, 'index1']);
    Route::get('productions/create-production', [PurchasesController::class, 'createProduction']);
    Route::post('productions/pt-production', [PurchasesController::class, 'pendingProduction']);
    Route::get('productions/api/stockprodtemp/{id}', [StockItemTempApiController::class, 'index']);
    Route::resource('productions/api/stockprodtemp', StockItemTempApiController::class);
    Route::get('purchase-items/{id}', [PurchasesController::class, 'purchaseItems']);
    Route::post('add-purchase-item', [PurchasesController::class, 'addItem']);
    Route::post('delete-multiple-purchases', [PurchasesController::class, 'deleteMultiple']);
    Route::resource('cost-items', PurchaseCostItemController::class);
    Route::post('set-supp-ob', [PurchasesController::class, 'setOpeningBalance']);
    Route::post('update-adjustment', [PurchasesController::class, 'updateAdjustment']);
    Route::resource('purchase-payments', PurchasePaymentController::class);
    Route::get('total-purchase-payments', [PurchasePaymentController::class, 'totalPurchasePayments']);
    Route::post('total-purchase-payments', [PurchasePaymentController::class, 'totalPurchasePayments']);
    Route::post('supplier-acc-payments', [PurchasePaymentController::class, 'accPayments']);
    Route::get('show-voucher/{pv_no}', [PurchasePaymentController::class, 'showVoucher']);
    Route::get('del-supp-trans/{id}', [PurchasesController::class, 'deleteTrans']);
    Route::get('del-acc-pv/{pv_no}', [PurchasePaymentController::class, 'deletePayment']);
    Route::resource('stocks', StockController::class);
    // Stocks Routes End
    // Damage Reoutes
    Route::resource('damages', ProdDamageController::class);
    // Damages routes End


    Route::get('accounting-dash', [AccountingDashController::class, 'index']);
    Route::post('accounting-dash', [AccountingDashController::class, 'index']);

    Route::post('filter-cash-flows', [CashOutController::class, 'index']);
    Route::resource('cash-flows', CashOutController::class);
    Route::post('cash-flows.index', [CashOutController::class, 'index']);
    Route::get('delete-cout/{id}', [CashOutController::class, 'destroy']);
    Route::resource('cash-ins', CashInController::class);
    Route::get('delete-cash-in/{id}', [CashInController::class, 'destroy']);

    Route::resource('acc-transactions', AccountTransController::class);
    Route::get('delete-acc-trans/{id}', [AccountTransController::class, 'destroy']);
    Route::resource('petty-cash', PettyCashController::class);
    Route::get('petty-cash/approve-petty-cash/{id}', [PettyCashController::class, 'approvePetty']);
    Route::get('petty-cash/confirm-petty-cash-receive/{id}', [PettyCashController::class, 'confirmReceived']);
    Route::post('reject-petty-cash', [PettyCashController::class, 'rejectPettyCash']);
    Route::get('petty-cash-report', [PettyCashController::class, 'pettyCashReport']);
    Route::post('petty-cash-report', [PettyCashController::class, 'pettyCashReport']);
    Route::get('collections-report', [ReportsController::class, 'collectionsReport']);
    Route::post('collections-report', [ReportsController::class, 'collectionsReport']);
    Route::get('business-value', [FinancialReportsController::class, 'BusinessValue']);
    Route::post('business-value', [FinancialReportsController::class, 'BusinessValue']);

    Route::resource('accounts', AccountsController::class);
    Route::resource('chart-of-accounts', COAController::class);


    // Expense Routes
    Route::resource('expense-categories', ExpenseCategoryController::class);
    Route::resource('expense-items', ExpenseItemController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::get('expenses/approve-expense/{id}', [ExpenseController::class, 'approveExpense']);
    Route::post('reject-expense', [ExpenseController::class, 'rejectExpense']);
    Route::resource('exp-suppliers', ExpSupplierController::class);
    Route::post('filter-expenses', [ExpenseController::class, 'index']);
    Route::post('store-expenses', [ExpenseController::class, 'storeExpense']);
    Route::get('exp-aging-report', [ExpenseController::class, 'agingReport']);
    Route::get('cancel-expense', [ExpenseController::class, 'cancel']);
    Route::post('delete-multiple-expenses', [ExpenseController::class, 'deleteMultiple']);
    Route::get('expense-account-stmt/{id}', [ExpenseController::class, 'accountStmt']);
    Route::post('expense-account-stmt/{id}', [ExpenseController::class, 'accountStmt']);
    Route::get('expenses/api/expenses', [ExpenseTempController::class, 'create']);
    Route::resource('expenses/api/expensetemp', ExpenseTempController::class);
    Route::resource('expense-payments', ExpensePaymentController::class);
    Route::post('f-expense-payments', [ExpensePaymentController::class, 'index']);
    Route::get('expense-payments/show-cn/{id}', [ExpensePaymentController::class, 'showCreditNote']);
    Route::get('expense-payments/delete-cn/{id}', [ExpensePaymentController::class, 'deleteCN']);
    Route::post('expense-payments/setOpeningBalance', [ExpensePaymentController::class, 'setOpeningBalance']);
    Route::post('expense-payments/update-adjustment', [ExpensePaymentController::class, 'updateAdjustment']);
    Route::get('expense-payments/inv-expenses/{id}', [ExpensePaymentController::class, 'invExpenses']);
    Route::get('expense-payments/delete-trans/{id}', [ExpensePaymentController::class, 'deletePayTrans']);
    Route::post('expense-payments/acc-payments', [ExpensePaymentController::class, 'accPayments']);
    Route::get('expense-payments/del-payment/{pv_no}', [ExpensePaymentController::class, 'deletePayment']);
    Route::get('del-exp-supp-trans/{id}', [ExpensePaymentController::class, 'deleteTrans']);

    //Utilizations
    Route::resource('exp-utilizations', ExpUtilizationController::class);
    // Expenses routes End

    // Customers Routes
    Route::get('search-customer', [CustomerController::class, 'autoSearch']);
    Route::get('excel-sample-customers', [CustomerController::class, 'download']);
    Route::post('import-customer', [CustomerController::class, 'import']);
    Route::resource('customers', CustomerController::class);
    Route::get('customers/delete-customer/{id}', [CustomerController::class, 'deleteCustomerWithData']);
    Route::get('activate-customer/{id}', [CustomerController::class, 'activateCustomer']);
    Route::post('customers/getShopCustomers', [CustomerController::class, 'getShopCustomers'])->name('customers.getShopCustomers');
    Route::get('export-customers', [CustomerController::class, 'export']);
    Route::post('new-customer', [CustomerController::class, 'createNew']);
    Route::resource('customer-categories', CustomerCategoryController::class);
    Route::get('delete-customer/{id}', [CustomerController::class, 'destroy']);
    Route::post('delete-multiple-customers', [CustomerController::class, 'deleteMultiple']);
    Route::resource('sms-notifications', SmsTemplateController::class);
    Route::post('sms-dynamic', [SmsTemplateController::class, 'dynamic']);
    Route::get('send-sms/{id}', [SmsTemplateController::class, 'show']);
    Route::get('sms-notifications/destroy/{id}', [SmsTemplateController::class, 'destroy']);
    Route::get('sms-settings', [SmsTemplateController::class, 'getSetting']);
    Route::post('sms-settings', [SmsTemplateController::class, 'settings']);
    // Customers Routes End


    Route::resource('emp-loans', EmployeeLoanController::class);
    Route::get('approve-loan/{id}', [EmployeeLoanController::class, 'approveLoan']);
    Route::get('issue-loan/{id}', [EmployeeLoanController::class, 'issueLoan']);
    Route::resource('emp-loan-returns', EmployeeLoanReturnController::class);

    Route::resource('payroll-deductions', PayrollDeductionController::class);
    Route::post('f-payroll-deductions', [PayrollDeductionController::class, 'index']);
    Route::resource('payroll-deduction-payments', PayrollDeductionPaymentController::class);

    Route::get('reconcile', [AccountsController::class, 'reconcile']);
    Route::post('reconcile', [AccountsController::class, 'reconcile']);
    Route::get('account-statements', [FinancialReportsController::class, 'accountStatements']);
    Route::post('account-statements', [FinancialReportsController::class, 'accountStatements']);
    Route::get('cash-flow-statement', [FinancialReportsController::class, 'CashFlowStatement']);
    Route::post('cash-flow-statement', [FinancialReportsController::class, 'CashFlowStatement']);
    Route::get('daily-cash-flow-statement', [FinancialReportsController::class, 'DailyCashFlowStatement']);
    Route::post('daily-cash-flow-statement', [FinancialReportsController::class, 'DailyCashFlowStatement']);
    Route::get('income-statement', [FinancialReportsController::class, 'IncomeStatement']);
    Route::post('income-statement', [FinancialReportsController::class, 'IncomeStatement']);
    Route::get('closing-business-value', [FinancialReportsController::class, 'MonthyClosingBusinessValue']);
    Route::post('closing-business-value', [FinancialReportsController::class, 'MonthyClosingBusinessValue']);
    Route::get('open-closing-amount-statement', [FinancialReportsController::class, 'OpenClosingAmoutStatement']);
    Route::post('open-closing-amount-statement', [FinancialReportsController::class, 'OpenClosingAmoutStatement']);
    Route::get('expenses-report', [FinancialReportsController::class, 'expenses']);
    Route::post('expenses-report', [FinancialReportsController::class, 'expenses']);

    //Sales Summary Reports
    Route::get('reports', [ReportsController::class, 'index']);
    Route::get('detailed-daily-profit-loss', [ReportsController::class, 'detailedProfitLoss']);
    Route::post('detailed-daily-profit-loss', [ReportsController::class, 'detailedProfitLoss']);
    Route::post('reports-by-date', [ReportsController::class, 'reportsByDate']);
    Route::get('monthly-sales-report', [ReportsController::class, 'monthlyTotalSales']);
    Route::post('monthly-sales-report', [ReportsController::class, 'monthlyTotalSales']);
    Route::get('daily-total-sales', [ReportsController::class, 'dailyTotalSales']);
    Route::post('daily-total-sales', [ReportsController::class, 'dailyTotalSales']);
    Route::get('sales-report', [ReportsController::class, 'sales']);
    Route::post('sales-report', [ReportsController::class, 'sales']);
    Route::get('rental-status-report', [AnSaleController::class, 'rentalStatus']);
    Route::post('rental-status-report', [AnSaleController::class, 'rentalStatus']);
    Route::get('sales-return-report', [ReportsController::class, 'salesReturns']);
    Route::post('sales-return-report', [ReportsController::class, 'salesReturns']);
    Route::get('debts-report', [ReportsController::class, 'debts']);
    Route::post('debts-report', [ReportsController::class, 'debts']);
    Route::get('single-expense-report/{type}', [ReportsController::class, 'singleExpenseReport']);
    Route::post('single-expense-report/{type}', [ReportsController::class, 'singleExpenseReport']);
    Route::get('profits', [ReportsController::class, 'profitReports']);
    Route::post('profits', [ReportsController::class, 'profitReports']);
    Route::get('sales-by-product', [ReportsController::class, 'salesByProduct']);
    Route::post('sales-by-product', [ReportsController::class, 'salesByProduct']);
    Route::post('sales-by-product', [ReportsController::class, 'salesByProduct']);
    Route::get('top-selling-products', [ReportsController::class, 'topSellingProducts']);
    Route::post('top-selling-products', [ReportsController::class, 'topSellingProducts']);
    Route::get('dreport-summary', [ReportsController::class,  'summaryReport']);
    Route::post('dreport-summary', [ReportsController::class,  'summaryReport']);

    Route::get('discount-by-sales', [ReportsController::class, 'discountBySales']);
    Route::post('discount-by-sales', [ReportsController::class, 'discountBySales']);
    Route::get('discount-by-product', [ReportsController::class, 'discountByProduct']);
    Route::post('discount-by-product', [ReportsController::class, 'discountByProduct']);

    Route::get('total-report', [ReportsController::class, 'totalAmounts']);
    Route::post('total-report', [ReportsController::class, 'totalAmounts']);
    Route::get('print-report', [ReportsController::class, 'printReport']);
    Route::get('serv-total-report', [ReportsController::class, 'totalAmounts']);
    Route::post('serv-total-report', [ReportsController::class, 'totalAmounts']);
    Route::get('consolidated', [ReportsController::class, 'consolidated']);
    Route::post('consolidated', [ReportsController::class, 'consolidated']);

    //Company Reports
    Route::get('company-reports', [CompanyReportsController::class, 'index']);
    Route::get('management-report', [CompanyReportsController::class, 'managementReport']);
    Route::post('f-management-report', [CompanyReportsController::class, 'managementReport']);
    Route::get('company-income-stmt', [CompanyReportsController::class, 'incomeStmt']);
    Route::post('f-company-income-stmt', [CompanyReportsController::class, 'incomeStmt']);
    Route::get('company-cf-stmt', [CompanyReportsController::class, 'cfStmt']);
    Route::post('f-company-cf-stmt', [CompanyReportsController::class, 'cfStmt']);
    Route::get('balance-sheet', [CompanyReportsController::class, 'balanceSheet']);
    Route::post('f-balance-sheet', [CompanyReportsController::class, 'balanceSheet']);
    Route::get('monthly-balance-sheet', [CompanyReportsController::class, 'monthlyBalanceSheet']);
    Route::post('f-monthly-balance-sheet', [CompanyReportsController::class, 'monthlyBalanceSheet']);

    Route::get('general-ledger', [CompanyReportsController::class, 'generalLedger']);
    Route::post('f-general-ledger', [CompanyReportsController::class, 'generalLedger']);
    Route::get('trial-balance', [CompanyReportsController::class, 'trialBalance']);
    Route::post('f-trial-balance', [CompanyReportsController::class, 'trialBalance']);

    Route::resource('balance-sheets', BalanceSheetsController::class);
    Route::post('f-balance-sheets', [BalanceSheetsController::class, 'index']);
    Route::get('basic-balance-sheets', [BalanceSheetsController::class, 'basic']);
    Route::get('basic-balance-sheets', [BalanceSheetsController::class, 'basic']);
    Route::post('update-bs-item', [BalanceSheetsController::class, 'updateBSItem']);
    Route::post('update-balance-sheet-item', [BalanceSheetsController::class, 'update']);
    Route::get('create-bs', [BalanceSheetsController::class, 'createBS']);

    // Inventory Reports
    Route::get('stock-reports', [StockReportController::class, 'index']);
    Route::post('stock-reports', [StockReportController::class, 'index']);
    Route::get('daily-closing-stock-report', [StockReportController::class, 'dcsValues']);
    Route::post('daily-closing-stock-report', [StockReportController::class, 'dcsValues']);
    Route::get('reorder-reports', [StockReportController::class, 'reorderReports']);
    Route::post('stock-taking', [StockReportController::class, 'stockTaking']);
    Route::get('stock-taking', [StockReportController::class, 'stockTaking']);
    Route::get('po-item-status-report', [StockReportController::class, 'poItemStatusReport']);
    Route::post('po-item-status-report', [StockReportController::class, 'poItemStatusReport']);
    Route::get('stock-capital', [StockReportController::class, 'stockCapital']);
    Route::post('stock-capital', [StockReportController::class, 'stockCapital']);
    Route::get('initial-stock-value', [StockReportController::class, 'initialStockCapital']);
    Route::get('stock-expires', [StockReportController::class, 'stockExpires']);
    Route::post('stock-expires', [StockReportController::class, 'stockExpires']);
    Route::get('transfer-report', [StockReportController::class, 'transfers']);
    Route::post('transfer-report', [StockReportController::class, 'transfers']);
    Route::get('transfer-received-report', [StockReportController::class, 'transfersReceived']);
    Route::post('transfer-received-report', [StockReportController::class, 'transfersReceived']);
    Route::post('stock-report-date', [StockReportController::class, 'reportsByDateRange']);
    Route::get('supplier-credit-reports', [PurchaseReportController::class, 'credits']);
    Route::post('supplier-credit-reports', [PurchaseReportController::class, 'credits']);
    Route::get('supplier-aging-report', [PurchaseReportController::class, 'agingReport']);

    //Charts
    Route::get('charts', [ChartsController::class, 'index']);
    Route::post('charts', [ChartsController::class, 'index']);

    // Service Business Routes
    Route::get('auto-service-code', [ServiceController::class, 'autoCode']);
    Route::resource('services', ServiceController::class);
    Route::get('services/destroy/{id}', [ServiceController::class, 'destroy']);
    Route::resource('chako-tours', ChakoTourController::class);
    Route::post('f-chako-tours', [ChakoTourController::class, 'index']);
    Route::resource('devices', DeviceController::class);
    Route::get('search-device', [DeviceController::class, 'autoSearch']);
    Route::get('devices/destroy/{id}', [DeviceController::class, 'destroy']);
    Route::get('trip-logs', [DeviceController::class, 'tripLogs']);
    Route::post('trip-logs', [DeviceController::class, 'tripLogs']);
    Route::resource('grades', GradeController::class);
    Route::get('grades/destroy/{id}', [GradeController::class, 'destroy']);
    Route::get('serv-reports', [ReportsController::class, 'index']);
    Route::post('serv-reports', [ReportsController::class, 'index']);

    Route::resource('trip-logs', TripLogsController::class);
    Route::post('f-trip-logs', [TripLogsController::class, 'index']);
    Route::get('create-invoice-for-trips/{id}', [TripLogsController::class, 'createInvoice']);
    Route::post('create-invoice-for-trips/{id}', [TripLogsController::class, 'createInvoice']);
    Route::post('trips-invoice', [TripLogsController::class, 'tripsInvoice']);
    Route::get('trip-logs-report', [TripLogsController::class, 'tripLogs']);
    Route::post('trip-logs-report', [TripLogsController::class, 'tripLogs']);


    // Hotel Routes
    Route::resource('rooms', RoomController::class);
    Route::resource('room-types', RoomTypeController::class);
    Route::resource('bookings', BookingController::class);
    Route::post('f-bookings', [BookingController::class, 'index']);
    Route::post('save-booking-changes', [BookingController::class, 'saveChanges']);
    Route::post('change-currency', [BookingController::class, 'changeCurrency']);
    Route::post('change-rate-mode', [BookingController::class, 'changeRateMode']);
    Route::post('update-foreign-ex-rate', [BookingController::class, 'saveForeignRate']);
    Route::post('update-local-ex-rate', [BookingController::class, 'saveLocalRate']);
    Route::get('cancel-booking/{id}', [BookingController::class, 'cancelBooking']);
    Route::post('add-selected-room', [BookingController::class, 'addRoom']);
    Route::get('remove-room/{id}', [BookingController::class, 'removeRoom']);
    Route::post('add-selected-service', [BookingController::class, 'addService']);
    Route::post('update-selected-service', [BookingController::class, 'updateService']);
    Route::get('remove-service/{id}', [BookingController::class, 'removeService']);
    Route::resource('booking-agents', BookingAgentController::class);
    Route::post('add-agent-rate', [BookingAgentController::class, 'addRate']);
    Route::post('update-agent-rate', [BookingAgentController::class, 'updateRate']);
    Route::get('remove-rate/{id}', [BookingAgentController::class, 'removeRate']);

    Route::resource('contracts', ContractController::class);
    Route::post('f-contracts', [ContractController::class, 'index']);
    Route::get('cancel-contract/{id}', [ContractController::class, 'cancelContract']);
    Route::post('terminate-contract', [ContractController::class, 'terminateContract']);
    Route::get('resume-contract/{id}', [ContractController::class, 'resumeContract']);
    Route::post('save-contract-changes', [ContractController::class, 'saveChanges']);
    Route::post('add-contract-service', [ContractController::class, 'addService']);
    Route::post('update-contract-service', [ContractController::class, 'updateService']);
    Route::get('remove-contract-service/{id}', [ContractController::class, 'removeService']);
    Route::resource('daily-deposits', DailyDepositController::class);
    Route::get('fetch-daily-deposits', [DailyDepositController::class, 'index']);
    Route::post('fetch-daily-deposits', [DailyDepositController::class, 'index']);

    Route::get('cm-dashboard', [CReportsController::class, 'index']);
    Route::post('cm-dashboard', [CReportsController::class, 'index']);
    Route::get('daily-deposit-report', [CReportsController::class, 'dailyDeposits']);
    Route::post('daily-deposit-report', [CReportsController::class, 'dailyDeposits']);
    Route::get('monthly-deposit-report', [CReportsController::class, 'monthlyDeposits']);
    Route::post('monthly-deposit-report', [CReportsController::class, 'monthlyDeposits']);
    Route::get('monthly-collection-report', [CReportsController::class, 'monthlyCollection']);
    Route::post('monthly-collection-report', [CReportsController::class, 'monthlyCollection']);
    Route::get('tl-daily-performance-report', [CReportsController::class, 'tlDailyPerformance']);
    Route::post('tl-daily-performance-report', [CReportsController::class, 'tlDailyPerformance']);
    Route::get('tl-monthly-performance-report', [CReportsController::class, 'tlMonthlyPerformance']);
    Route::post('tl-monthly-performance-report', [CReportsController::class, 'tlMonthlyPerformance']);
    Route::get('tl-monthly-collection-report', [CReportsController::class, 'tlMonthlyCollection']);
    Route::post('tl-monthly-collection-report', [CReportsController::class, 'tlMonthlyCollection']);
    Route::get('monthly-profit-report', [CReportsController::class, 'monthlyProfit']);
    Route::post('monthly-profit-report', [CReportsController::class, 'monthlyProfit']);
    Route::get('contract-status-report', [CReportsController::class, 'contractStatusReport']);
    Route::post('contract-status-report', [CReportsController::class, 'contractStatusReport']);
    Route::get('working-riders-report', [CReportsController::class, 'workingRiders']);
    Route::post('working-riders-report', [CReportsController::class, 'workingRiders']);
    Route::get('upcoming-graduation-report', [CReportsController::class, 'upcomingGraduation']);
    Route::post('upcoming-graduation-report', [CReportsController::class, 'upcomingGraduation']);
    Route::get('contract-to-terminate', [CReportsController::class, 'contractToTerminate']);
    Route::post('contract-to-terminate', [CReportsController::class, 'contractToTerminate']);
    Route::get('over-deposited', [CReportsController::class, 'overDeposited']);
    Route::post('over-deposited', [CReportsController::class, 'overDeposited']);
    Route::get('monthly-reg-report', [CReportsController::class, 'monthlyRegReport']);
    Route::post('monthly-reg-report', [CReportsController::class, 'monthlyRegReport']);

    Route::get('serv-sales-report', [ReportsController::class, 'sales']);
    Route::post('serv-sales-report', [ReportsController::class, 'sales']);
    Route::get('serv-debts-report', [ReportsController::class, 'debts']);
    Route::post('serv-debts-report', [ReportsController::class, 'debts']);
    Route::get('serv-expenses-report', [ReportsController::class, 'expenses']);
    Route::post('serv-expenses-report', [ReportsController::class, 'expenses']);
    Route::get('sales-by-service', [ReportsController::class, 'salesByService']);
    Route::post('sales-by-service', [ReportsController::class, 'salesByService']);
    Route::get('both-reports', [ReportsController::class, 'index']);
    Route::post('both-reports', [ReportsController::class, 'index']);


    //Agent OCamount routes
    Route::resource('ocamounts', OCamountController::class);
    Route::post('oc-amounts', [OCAmountController::class, 'index']);
    Route::get('delete-ocamount/{id}', [OCAmountController::class, 'destroy']);
    Route::post('delete-multiple-ocamounts', [OCAmountController::class, 'deleteMultiple']);

    // Production Routes
    Route::get('prod-dash', [ProdHomeController::class, 'index']);
    Route::post('prod-dash', [ProdHomeController::class, 'index']);

    //Raw Materials
    Route::resource('raw-materials', RawMaterialController::class);
    Route::post('rm-new-buy-price', [RawMaterialController::class, 'newBuyPrice']);
    Route::post('new-rm-reorder-point', [RawMaterialController::class, 'newReorderPoint']);
    Route::post('delete-multiple-materials', [RawMaterialController::class, 'deleteMultiple']);
    Route::resource('rm-purchases', RmPurchaseController::class);
    Route::post('delete-multiple-rm-purchases', [RmPurchaseController::class, 'deleteMultiple']);
    Route::get('cancel-rmitem', [RmPurchaseController::class, 'cancel']);
    Route::resource('rm-items', RmItemController::class);
    Route::get('rm-purchases-grn/{id}', [RmPurchaseController::class, 'purchaseGRN'])->name('rm-purchase-grn');
    Route::resource('rm-purchases/api/rmitemtemp', RmPurchaseItemApiController::class);
    Route::get('rm-purchases/api/rmitem', [RawMaterialApiController::class, 'index']);
    Route::put('rm-purchases/rm-temp/{id}', [RmPurchaseController::class, 'updateTemp']);
    Route::resource('rm-purchase-payments', RmPurchasePaymentController::class);
    Route::post('rm-suppliers-transaction/show', [RmSupplierTransactionController::class, 'show']);
    Route::post('pm-suppliers-transaction/show', [PmSupplierTransactionController::class, 'show']);
    Route::resource('rm-suppliers-transaction', RmSupplierTransactionController::class);
    Route::get('del-rm-acc-pv/{id}', [RmSupplierTransactionController::class, 'deletePayment']);
    Route::resource('pm-suppliers-transaction', PmSupplierTransactionController::class);
    Route::get('del-pm-acc-pv/{id}', [PmSupplierTransactionController::class, 'deletePayment']);

    Route::resource('rm-uses', RmUseController::class);
    Route::post('rm-uses-store', [RmUseController::class, 'storeProduct']);
    Route::get('delete-rmuse/{id}', [RmUseController::class, 'destroy']);
    Route::resource('rm-uses/api/rmuitem', RawMaterialApiController::class);
    Route::resource('rm-uses/api/rmusedtemp', RmUseItemTempController::class);
    Route::resource('rm-used-items', RmUsedItemController::class);
    Route::resource('rm-damages', RmDamageController::class);

    Route::post('rm-purchase-payments/accPayments', [RmPurchasePaymentController::class, 'accPayments']);
    Route::post('rm-purchase-payments/update-adjustment', [RmPurchasePaymentController::class, 'updateAdjustment']);
    Route::post('rm-purchase-payments/setOpeningBalance', [RmPurchasePaymentController::class, 'setOpeningBalance']);
    Route::get('rm-purchase-payments/show-voucher/{pv_no}', [RmPurchasePaymentController::class, 'showVoucher']);
    Route::get('rm-purchase-payments/previewVoucher', [RmPurchasePaymentController::class, 'previewVoucher']);
    Route::get('rm-purchase-payments/delete-supp-trans/{id}', [RmPurchasePaymentController::class, 'deleteTrans']);

    //Packing Materials
    Route::resource('packing-materials', PackingMaterialController::class);
    Route::post('pm-new-buy-price', [PackingMaterialController::class, 'newBuyPrice']);
    Route::resource('pm-purchases', PmPurchaseController::class);
    Route::post('delete-multiple-pm-purchases', [PmPurchaseController::class, 'deleteMultiple']);
    Route::resource('pm-purchases/api/pmitem', PackingMaterialApiController::class);
    Route::resource('pm-purchases/api/pmitemtemp', PmPurchaseItemApiController::class);
    Route::put('pm-purchases/pm-temp/{id}', [PmPurchaseController::class, 'updateTemp']);
    Route::get('cancel-pmitem', [PmPurchaseController::class, 'cancel']);
    Route::get('pm-purchase-items/{id}', [PmPurchaseController::class, 'purchaseItems']);
    Route::get('delete-pmpurchase/{id}', [PmPurchaseController::class, 'destroy']);
    Route::resource('pm-purchase-payments', PmPurchasePaymentController::class);
    Route::resource('pm-items', PmItemController::class);

    Route::resource('pm-uses', PmUseController::class);
    Route::resource('pm-uses/api/pmuitem', PackingMaterialApiController::class);
    Route::resource('pm-uses/api/pmusedtemp', PmUseItemTempController::class);
    Route::resource('pm-used-items', PmUsedItemController::class);
    Route::post('pm-uses/api/saveprodtemp', [PmUseItemTempController::class, 'saveProdTemp']);

    //PM Transfers
    Route::resource('pm-transfers', PmTransferController::class);
    Route::post('f-pm-transfers', [PmTransferController::class, 'index']);
    Route::resource('pm-transfer-items', PmTransferItemController::class);
    Route::post('update-pm-transfer-item', [PmTransferItemController::class, 'update']);
    Route::get('receive-pm-transfer/{id}', [PmTransferController::class, 'receiveForm']);
    Route::post('receive-pm-transfer', [PmTransferController::class, 'receivePM']);
    Route::post('update-pm-transfer-item-rec-qty', [PmTransferItemController::class, 'updateRec']);

    Route::resource('pm-damages', PmDamageController::class);
    Route::get('pm-damages', [PmDamageController::class, 'store']);
    Route::get('delete-pmdamage/{id}', [PmDamageController::class, 'destroy']);

    Route::post('new-pm-reorder-point', [PackingMaterialController::class, 'newReorderPoint']);
    Route::get('pm-supplier-account-stmt/{id}', [PmPurchaseController::class, 'accountStmt']);
    Route::post('pm-supplier-account-stmt/{id}', [PmPurchaseController::class, 'accountStmt']);
    Route::post('pm-purchase-payments/accPayments', [PmPurchasePaymentController::class, 'accPayments']);
    Route::post('pm-purchase-payments/update-adjustment', [PmPurchasePaymentController::class, 'updateAdjustment']);
    Route::post('pm-purchase-payments/setOpeningBalance', [PmPurchasePaymentController::class, 'setOpeningBalance']);
    Route::get('pm-purchase-payments/show-voucher/{pv_no}', [PmPurchasePaymentController::class, 'showVoucher']);
    Route::get('pm-purchase-payments/previewVoucher', [PmPurchasePaymentController::class, 'previewVoucher']);
    Route::get('pm-purchase-payments/delete-supp-trans/{id}', [PmPurchasePaymentController::class, 'deleteTrans']);

    //MRO Items
    Route::resource('mro', MROController::class);
    Route::resource('mro-items', MROItemController::class);
    Route::post('mro-items', [MROItemController::class, 'index']);
    Route::resource('mro-used-items', MROItemController::class);
    Route::resource('mro-uses', MroUseController::class);
    Route::resource('mro-uses/api/mrousedtemp', MroUsedItemTempController::class);
    Route::resource('mro-uses/api/mro-uitems', MroApiController::class);

    Route::resource('production-stages', ProductionStageController::class);
    Route::resource('pp-stages', PPStageController::class);
    Route::resource('prod-labour-costs', ProdLabourCostController::class);
    Route::post('f-prod-labour-costs', [ProdLabourCostController::class, 'index']);
    Route::resource('prod-labour-costs/api/prod-labourcosttemp', ProdLabourCostTempController::class);
    Route::get('cancel-labourcost', [ProdLabourCostController::class, 'cancel']);
    Route::resource('plc-items', ProdLabourCostItemController::class);
    Route::post('update-plc-item', [ProdLabourCostItemController::class, 'update']);
    Route::resource('plc-payments', PlcPaymentController::class);
    Route::post('f-plc-payments', [PlcPaymentController::class, 'index']);

    Route::resource('moh-costs', MohCostController::class);
    Route::post('f-moh-costs', [MohCostController::class, 'index']);
    Route::resource('moh-costs/api/moh-costtemp', MohCostTempController::class);
    Route::get('cancel-mohcost', [MohCostController::class, 'cancel']);
    Route::resource('moh-items', MohCostItemController::class);
    Route::post('update-moh-item', [MohCostItemController::class, 'update']);
    Route::resource('moh-payments', MohCostPaymentController::class);
    Route::post('f-moh-payments', [MohCostPaymentController::class, 'index']);

    //Production cost
    Route::resource('product-pricings', ProductPricingController::class);
    Route::post('product-pricings/create', [ProductPricingController::class, 'create']);
    Route::post('product-pricings/edit', [ProductPricingController::class, 'edit']);
    Route::get('cancel-pricing/{id}', [ProductPricingController::class, 'destroy']);
    Route::post('product-pricings/api/update-pricing/{id}', [ProductPricingController::class, 'update']);
    Route::get('product-pricings/api/material-costs/{id}', [MaterialCostController::class, 'index']);
    Route::resource('product-pricings/api/material-costs', MaterialCostController::class);
    Route::resource('product-pricings/api/labour-costs', LabourCostController::class);
    Route::resource('product-pricings/api/transport-costs', TransportCostController::class);
    Route::resource('product-pricings/api/indirect-costs', IndirectCostController::class);
    Route::resource('product-pricings/api/local-indirect-costs', LocalIndirectCostController::class);
    Route::resource('product-pricings/api/packaging-costs', PackagingCostController::class);
    Route::resource('product-pricings/api/local-packaging-costs', LocalPackagingCostController::class);
    Route::resource('product-pricings/api/export-handling-costs', ExportHandlingCostController::class);
    Route::get('set-product-price/{id}', [ProductPricingController::class, 'setProductPrice']);

    Route::post('f-prod-costs' , [ProductionCostController::class, 'index']);
    Route::resource('prod-costs', ProductionCostController::class);
    Route::post('prod-costs/api/prod-items/create', [ProductionApiController::class, 'create']);
    Route::get('prod-costs/api/product-made', [ProductionApiController::class, 'product_made']);
    Route::get('prod-costs/api/prod-items/recalculate', [ProductionApiController::class, 'recalculate']);
    Route::resource('prod-costs/api/prod-items', ProductionApiController::class);
    Route::resource('prod-costs/api/prod-mrousedtemp', MroUsedItemTempController::class);
    Route::resource('prod-costs/api/prod-rmusedtemp', RmUseItemTempController::class);
    Route::resource('prod-costs/api/prod-dlctemp', DlcItemTempController::class);
    Route::resource('prod-costs/api/prod-pmusedtemp', PmUseItemTempController::class);
    Route::post('prod-costs/savepanel', [ProductionCostController::class, 'savePanel']);
    Route::get('cancel-prod-panel', [ProductionCostController::class, 'cancelProduction']);
    Route::post('production/createOld', [ProductionCostController::class, 'createold']);
    Route::get('production/createOld', [ProductionCostController::class, 'createold']);

    Route::resource('dlc-items', DLCItemController::class);
    Route::resource('prod-cost-items', ProdCostItemController::class);

    // Food Production
    Route::resource('food-types', FoodTypeController::class);
    Route::resource('food-productions', FoodProductionController::class);
    Route::post('f-food-productions', [FoodProductionController::class, 'index']);
    Route::resource('food-productions/api/fp-rmusetemp', FoodProductionTempController::class);
    Route::post('set-food-type', [FoodProductionController::class, 'setFoodType']);
    Route::get('cancel-food-production', [FoodProductionController::class, 'cancel']);
    Route::post('add-rm-use-item', [FoodProductionController::class, 'addItem']);
    Route::post('update-rm-use-item', [FoodProductionController::class, 'updateRmUseItem']);
    Route::get('remove-rm-use-item/{id}', [FoodProductionController::class, 'removeItem']);

    Route::resource('prod-wips', WIPsController::class);
    Route::post('f-prod-wips', [WIPsController::class, 'index']);
    Route::post('delete-multiple-wips', [WIPsController::class, 'deleteMultiple']);
    Route::resource('prod-wips/api/wiptemp', WIPTempController::class);
    Route::get('cancel-wip', [WIPsController::class, 'cancel']);

    Route::resource('prod-wip-materials', WIPMaterialController::class);
    Route::resource('prod-mwips', MaterialWIPsController::class);
    Route::post('f-prod-mwips', [MaterialWIPsController::class, 'index']);
    Route::post('delete-multiple-mwips', [MaterialWIPsController::class, 'deleteMultiple']);
    Route::resource('prod-mwips/api/mwiptemp', MaterialWIPTempController::class);
    Route::get('cancel-mwip', [MaterialWIPsController::class, 'cancel']);

    //Production Reports
    Route::get('pm-purchases-report', [ProdReportsController::class, 'PmPurchases']);
    Route::get('rm-purchases-report', [ProdReportsController::class, 'PmPurchases']);
    Route::post('pm-purchases-report', [ProdReportsController::class, 'PmPurchases']);
    Route::post('rm-purchases-report', [ProdReportsController::class, 'RmPurchases']);
    Route::get('prod-stock-status-report', [ProdReportsController::class, 'StockStatus']);
    Route::get('rm-uses-report', [ProdReportsController::class, 'RmUsesReport']);
    Route::post('rm-uses-report', [ProdReportsController::class, 'RmUsesReport']);
    Route::get('pm-uses-report', [ProdReportsController::class, 'PmUsesReport']);
    Route::post('pm-uses-report', [ProdReportsController::class, 'PmUsesReport']);
    Route::get('general-report', [ProdReportsController::class, 'generalReport']);
    Route::post('general-report', [ProdReportsController::class, 'generalReport']);
    //Production transfer
    Route::get('prod-transfer-to/{id}', [ProdTransferController::class, 'index'])->name('prod-transfer-to');
    Route::post('prod-transfer-store', [ProdTransferController::class, 'store'])->name('prod-transfer-store');

    // Asset & Depreciation Routes
    Route::resource('depreciations', DepreciationController::class);
    Route::resource('dep-methods', DepreciationMethodController::class);
    Route::resource('asset-records', AssetRecordController::class);

    //VFD
    Route::resource('vfd-reg-infos', RegInfoController::class);
    Route::get('send-reg-info/{id}', [RegInfoController::class, 'sendRegInfo']);
    Route::resource('vfd-rct-infos', RctInfoController::class);
    Route::get('submit-receipt/{id}', [RctInfoController::class, 'submitReceipt']);
    Route::resource('vfd-zreports', ZReportController::class);

    //HR & Payrolls
    Route::resource('my-profile', ProfileController::class);
    Route::get('my-pay-slips', [ProfileController::class, 'index']);
    Route::post('f-my-pay-slips', [ProfileController::class, 'index']);
    Route::get('hr-dash', [HRDashController::class, 'index']);
    Route::post('hr-dash', [HRDashController::class, 'index']);
    //HR
    Route::resource('positions', PositionController::class);
    Route::get('employees/face-id', [EmployeeFaceIdController::class, 'index'])->name('employees.face-id.index');
    Route::delete('employees/{id}/face-id', [EmployeeFaceIdController::class, 'destroy'])->name('employees.face-id.destroy');
    Route::resource('employees', EmployeeController::class);
    Route::get('employee-sample', [EmployeeController::class, 'downloadSample']);
    Route::post('import-employees', [EmployeeController::class, 'importEmployees']);
    Route::get('auto-id' , [EmployeeController::class , 'AutoID']);
    Route::resource('next-of-kins' , NextOfKinController::class);
    Route::resource('academic-infos' , AcademicInfoController::class);
    Route::resource('medical-infos' , EmployeeMedicalInfoController::class);
    Route::resource('leave-rosters' , LeaveRosterController::class);
    Route::get('leave-approve/{id}' , [LeaveRosterController::class , 'approve']);
    Route::post('leave-reject' , [LeaveRosterController::class , 'reject'])->name('leave-reject');
    Route::post('request-leave' , [LeaveRosterController::class , 'storeRequest'])->name('request-leave');
    Route::post('request-leave/update' , [LeaveRosterController::class , 'updateRequest'])->name('update-leave');
    Route::resource('hr-attendance' , AttendanceController::class);
    Route::post('hr-attendance' , [AttendanceController::class , 'index']);
    Route::post('attendance-punchin' , [AttendanceController::class , 'punchIn'])->name('attendance-punchin');
    Route::post('attendance-punchout' , [AttendanceController::class , 'punchOut'])->name('attendance-punchout');
    Route::resource('attendance-setting' , AttendanceSettingController::class);
    Route::resource('hr-departments' , DepartmentController::class);
    Route::post('add-dept' , [DepartmentController::class , 'addMember']);
    Route::get('remove-dept/{emp_id}/{dept_id}' , [DepartmentController::class , 'removeMember'])->name('remove-dept');
    Route::resource('hr-holidays' , HolidayController::class);
    Route::post('save-events' , [EventController::class, 'store']);
    Route::post('fetch-events' , [EventController::class, 'create']);
    Route::post('update-events/{id}' , [EventController::class , 'updateEvent']);
    Route::post('delete-events/{id}' , [EventController::class , 'destroy']);
    Route::resource('hr-events' , EventController::class);
    Route::post('remove-participants', [EventController::class, 'removeParticipants']);
    Route::post('f-hr-salaries', [EmployeeSalaryController::class, 'index']);
    Route::resource('hr-salaries' , EmployeeSalaryController::class);
    Route::resource('employee-docs' , EmployeeDocController::class);
    Route::get('/employees/download-id/{id}', [EmployeeController::class, 'downloadIdCard'])->name('employees.download_id');
    Route::get('employees/{id}/id-card', [EmployeeController::class, 'showIdCard'])->name('employees.id_card');
    Route::get('employees/print/selected-id-card',[EmployeeController::class, 'printSelectedIdCard'])
    ->name('employees.print.selected-id-card');
    //Payrolls
    Route::resource('payrolls', PayrollController::class);
    Route::post('payrolls/create', [PayrollController::class, 'create']);
    Route::post('payroll-list', [PayrollController::class, 'index']);
    Route::get('payroll-edit/{id}', [PayrollController::class, 'editPayroll']);
    Route::post('update-payroll', [PayrollController::class, 'updatePayroll']);
    Route::get('payroll-dash', [PayrollController::class, 'dashboard'])->middleware('permission:access-general-payroll-module');
    Route::post('payroll-dash', [PayrollController::class, 'dashboard']);
    Route::get('payroll-reports', [PayrollController::class, 'reports']);
    Route::post('payroll-reports', [PayrollController::class, 'reports']);
    Route::get('view-payroll/{id}', [PayrollController::class, 'viewPayroll']);
    Route::get('payroll-delete/{id}', [PayrollController::class, 'deletePayroll']);
    Route::resource('payrolls/api/payrolltemp', PayrollTempController::class);
    Route::get('cancel-payroll', [PayrollController::class, 'cancelPayroll']);
    Route::resource('payroll-settings', PayrollSettingsController::class);

    Route::get('add-to-expenses/{id}', [PayrollToExpenseController::class, 'create']);

    //MHC
    Route::get('mhc-dashboard', [DashController::class, 'index']);
    Route::post('mhc-dashboard', [DashController::class, 'index']);
    Route::resource('doctors', DoctorController::class);
    Route::resource('patients', PatientController::class);
    Route::get('search-patient', [PatientController::class, 'autoSearch']);
    Route::resource('appointments', AppointmentController::class);
    Route::post('f-appointments', [AppointmentController::class, 'index']);
    Route::post('assign-doctor', [AppointmentController::class, 'assignDoctor']);
    Route::get('create-bill/{id}', [AppointmentController::class, 'createBill']);
    Route::get('appointment-bill/{id}', [AppointmentController::class, 'appointmentBill']);
    Route::resource('appointment-services', AppointmentServiceController::class);
    Route::post('update-appmt-service-item', [AppointmentServiceController::class, 'update']);
    Route::resource('appointment-products', AppointmentProductController::class);
    Route::post('update-appmt-product-item', [AppointmentProductController::class, 'update']);
    Route::get('get/subtypes/{id}', [PatientController::class, 'getSubTypes']);
    Route::get('close-appointment/{id}', [AppointmentController::class, 'closeAppointment']);
    Route::get('start-diagnosis/{id}', [MedicalHistoryController::class, 'create']);
    Route::resource('medical-histories', MedicalHistoryController::class);
    Route::post('f-medical-histories', [MedicalHistoryController::class, 'index']);

    //Production Routitn
    Route::get('sand-prod-dash', [SandDashController::class, 'index']);
    Route::post('sand-prod-dash', [SandDashController::class, 'index']);

    Route::resource('washing-plants', WashingPlantController::class);
    Route::resource('washing-equipments', WashingEquipmentController::class);
    Route::resource('maintenance-records', MaintenanceRecordController::class);
    Route::resource('storage-locations', StorageLocationController::class);

    Route::resource('raw-material-sources', RawMaterialSourceController::class);
    Route::resource('rm-sourcings', RMSourcingController::class);
    Route::post('f-rm-sourcings', [RMSourcingController::class, 'index']);

    Route::resource('sand-productions', ProductionRunController::class);
    Route::post('f-sand-productions', [ProductionRunController::class, 'index']);
    Route::resource('quality-tests', QualityTestController::class);

    Route::resource('quote-requests', QuoteRequestController::class);
    Route::resource('quotations', QuotationController::class);

    //OrdersProcessing
    Route::resource('orders', OrderController::class);
    Route::post('f-orders', [OrderController::class, 'index']);
    Route::get('create-order-invoice/{id}', [OrderController::class, 'createInvoice']);
    Route::resource('order-deliveries', OrderDeliveryController::class);
    Route::post('f-order-deliveries', [OrderDeliveryController::class, 'index']);
    Route::post('add-selected-item', [OrderDeliveryController::class, 'addDeliveryItem']);
    Route::post('update-delivery-item', [OrderDeliveryController::class, 'updateDeliveryItem']);
    Route::post('update-order-status', [OrderController::class, 'updateOrderStatus']);
    Route::resource('order-payments', OrderPaymentController::class);
    Route::post('card-payments', [OrderPaymentController::class, 'initiateOrder']);
    Route::post('check-payment-order-status', [OrderPaymentController::class, 'checkOrderStatus']);
    Route::get('vms/total-vehicles', [VehicleController::class, 'totalVehicles']);
    Route::get('vms/active-trips', [VehicleController::class, 'activeTrips']);
    Route::get('vms/total-expenses', [VehicleController::class, 'totalExpenses']);
    Route::get('vms/pending-requests', [VehicleController::class, 'pendingRequests']);

    Route::get('vehicles-dash', [VehicleController::class, 'dashboard']);
    Route::post('f-vehicles-dash', [VehicleController::class, 'dashboard']);
    Route::post('vehicles/prepare-documents', [VehicleController::class, 'prepareDocuments'])->name('vehicles.prepare-documents');
    Route::get('vehicles/documents-step', [VehicleController::class, 'createDocumentsStep'])->name('vehicles.documents.create');
    Route::post('vehicles/documents-step', [VehicleController::class, 'storeWithDocuments'])->name('vehicles.documents.store');
    Route::resource('vehicles', VehicleController::class);
    Route::post('f-vehicles', [VehicleController::class, 'index'])->name('vehicles.filter');
    Route::resource('vehicle-types', VehicleTypeController::class);
    Route::post('ownerships/{id}/toggle-active', [OwnershipController::class, 'toggleActive'])->name('ownerships.toggle-active');
    Route::resource('ownerships', OwnershipController::class);

    // IMPORTANT: place literal routes before resource routes.
    // Otherwise `/legal-documents/status` may be matched by `legal-documents/{id}`.
    Route::get('legal-documents/status', [LegalDocumentController::class, 'vehicleStatus'])
        ->name('legal-documents.status');
    Route::resource('legal-documents', LegalDocumentController::class)
        ->whereNumber('legal_document');
    Route::post('f-legal-documents', [LegalDocumentController::class, 'index'])->name('legal-documents.filter');
    Route::get('legal-documents/{id}/download', [LegalDocumentController::class, 'download'])->name('legal-documents.download');
    Route::Put('vehicle-requisitions/{id}',[VehicleRequisitionController::class,'resubmit'])->name('vehicle-requisitions.resubmit');
    Route::resource('insurance', InsuranceController::class);
    Route::post('f-insurance', [InsuranceController::class, 'index'])->name('insurance.filter');
    Route::get('insurance/{id}/download', [InsuranceController::class, 'download'])->name('insurance.download');
    Route::resource('insurance-companies', InsuranceCompanyController::class)->except(['show', 'create']);
    Route::resource('ir-periods', IrPeriodController::class)->except(['show', 'create']);
    Route::resource('vehicle-requisitions', VehicleRequisitionController::class);
    Route::post('f-vehicle-requisitions', [VehicleRequisitionController::class, 'index'])->name('vehicle-requisitions.filter');
    Route::put('/vehicle-requisitions/{vehicle_requisition}/assign-driver',[VehicleRequisitionController::class, 'assignDriver'])
    ->name('vehicle-requisitions.assign-driver');
    Route::put('/vehicle-requisitions/{id}/reject', [VehicleRequisitionController::class, 'rejectRequisition'])
    ->name('vehicle-requisitions.reject');
    Route::resource('requisitions-purpose', RequisitionPurposeController::class);
    Route::resource('requisition-trip-logs', RequisitionTripLogController::class);
    Route::post('f-requisition-trip-logs', [RequisitionTripLogController::class, 'index'])->name('requisition-trip-logs.filter');
    Route::post('trip-start/{id}',[RequisitionTripLogController::class, 'tripStart'])->name('trip.start');
    Route::post('trip-end/{id}',[RequisitionTripLogController::class, 'endTrip'])->name('trip.end');

    Route::post('maintenace-approve/{id}', [MaintenanceController::class,'approve'])->name('maintenance.approve');
    Route::post('maintenance-reject/{id}', [MaintenanceController::class,'reject'])->name('maintenance.reject');
    Route::post('maintenance-complete/{id}', [MaintenanceController::class,'complete'])->name('maintenance.complete');
    Route::post('maintenance-start/{id}', [MaintenanceController::class,'start'])->name('maintenance.start');
    Route::resource('maintenance', MaintenanceController::class);
    Route::post('f-maintenance', [MaintenanceController::class, 'index'])->name('maintenance.filter');
    Route::resource('maintenance-types',MaintenanceTypeController::class);
    Route::resource('refueling', RefuelingController::class);
    Route::post('f-refueling', [RefuelingController::class, 'index'])->name('refueling.filter');
    Route::resource('fuel-types', FuelTypeController::class);
    Route::resource('fuel-stations',FuelStationController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('license-types', LicenseTypeController::class);
    Route::resource('vendors', VendorController::class);
    Route::post('f-vendors', [VendorController::class, 'index'])->name('vendors.filter');
    Route::post('vendor-account-stmt/{id}', [VendorController::class, 'show']);
    Route::post('vendor-acc-payments', [VendorController::class, 'accPayments']);
    Route::get('del-vendor-acc-pv/{id}', [VendorController::class, 'deletePayment']);
    Route::get('del-vendor-trans/{id}', [VendorController::class, 'deleteTrans']);
    Route::resource('parts', PartsController::class);
    Route::get('verify-av-qty/{id}', [PartsController::class, 'verifyAvQty']);
    Route::get('search-part', [PartsController::class, 'autoSearch']);
    Route::get('fetch-part', [PartsController::class, 'fetchPart']);
    Route::resource('part-categories', PartCategoryController::class);
    Route::resource('part-locations', PartLocationController::class);
    Route::resource('part-purchases', PartPurchaseController::class);
    Route::post('f-part-purchases', [PartPurchaseController::class, 'index']);
    Route::resource('part-purchase-items', PartPurchaseItemController::class);
    Route::get('pp-items/{id}', [PartPurchaseController::class, 'items']);
    Route::get('part-purchases/api/parttemp/{id}', [PartItemTempApiController::class, 'index']);
    Route::resource('part-purchases/api/parttemp', PartItemTempApiController::class);
    Route::post('part-purchases/api/update-part-purchase-temp', [PartItemTempApiController::class, 'updatePurchaseTemp']);
    Route::post('part-purchases/ppt-purchase', [PartPurchaseController::class, 'pendingPurchase'])->name('purchases/ppt-purchase');
    Route::get('cancel-part-purchase/{id}', [PartItemTempApiController::class, 'cancelPurchase']);
    Route::post('delete-multiple-part-purchases', [PartPurchaseController::class, 'deleteMultiple']);

    Route::resource('parts-usage', PartsUsageController::class);
    Route::post('f-parts-usage', [PartsUsageController::class, 'index']);
    Route::resource('parts-usage-items', PartUsageItemController::class);
    Route::post('update-pu-item', [PartUsageItemController::class, 'update']);
    Route::post('reject-part-usage', [PartsUsageController::class, 'rejectPURequest']);
    Route::get('parts-usage/approve-part-usage/{id}', [PartsUsageController::class, 'approvePURequest']);
    Route::get('parts-usage/close-part-usage/{id}', [PartsUsageController::class, 'closePURequest']);
    Route::resource('expense-type',ExpenseTypeController::class);
    Route::resource('vms-expenses', VmsExpenseController::class);
    Route::post('f-vms-expenses', [   VmsExpenseController::class, 'index']);
    Route::delete('vms-expense-attachment/{id}', [VmsExpenseController::class, 'destroyAttachment'])
     ->name('vms-expense-attachment.destroy');
    Route::get('cancel-vms-expense/{id}',  [VmsExpenseController::class, 'destroy']);
    Route::post('approve-vms-expense/{id}', [VmsExpenseController::class, 'approveExpense'])->name('approve-vms-expense');
    Route::post('reject-vms-expense',      [VmsExpenseController::class, 'rejectExpense'])->name('reject-vms-expense');
    Route::get('close-vms-expense/{id}',   [VmsExpenseController::class, 'closeExpense']);
    Route::post('resume-vms-expense',      [VmsExpenseController::class, 'resumePending']);

    // AJAX endpoints (add to an AjaxController or inline)
    Route::get('search-expense-type',   [ExpenseAjaxController::class, 'searchExpenseType']);
    Route::get('fetch-expense-type',    [ExpenseAjaxController::class, 'fetchExpenseType']);
    Route::get('fetch-expense-types',   [ExpenseAjaxController::class, 'fetchExpenseTypes']);
    Route::get('fetch-expense-items',   [ExpenseAjaxController::class, 'fetchExpenseItems']);
    Route::post('add-expense-item',     [ExpenseAjaxController::class, 'addExpenseItem']);
    Route::post('update-expense-item',  [ExpenseAjaxController::class, 'updateExpenseItem']);
    Route::post('remove-expense-item',  [ExpenseAjaxController::class, 'removeExpenseItem']);
    Route::resource('trip-types',TripTypeController::class);
    //visitor route
    Route::get('visitors-dash', [VisitorController::class, 'dashboard'])->name('visitors.dashboard');
    Route::post('visitors-dash', [VisitorController::class, 'dashboard']);
    Route::get('visitor', [VisitorController::class, 'index'])->name('visitors.index');
    Route::post('visitor/filter', [VisitorController::class, 'index'])->name('visitors.filter');
    Route::get('/visitors/export', [VisitorExportController::class, 'export'])->name('visitors.export');
    Route::get('/visitors/list', [VisitorController::class, 'list'])->name('visitors.list');

    Route::get('add-visitor', [VisitorController::class, 'create']);
    Route::post('visitor', [VisitorController::class, 'store'])->name('visitors.store');
    Route::get('visitors/{id}', [VisitorController::class, 'show'])->name('visitors.show');

    Route::get('edit-visitor/{id}', [VisitorController::class, 'edit'])->name('visitors.edit');
    Route::patch('update-visitor/{id}', [VisitorController::class, 'update'])->name('visitors.update');
    Route::delete('delete-visitor/{id}', [VisitorController::class, 'destroy'])->name('visitors.destroy');
    Route::get('grant-permission/{id}', [VisitorController::class, 'grantPermission']);
    // RecycleBin Routes
    Route::post('del-multiple-recycle-sales', [RecycleBinController::class, 'delMultipleRecycleSales']);
    Route::post('del-multiple-recycle-purchases', [RecycleBinController::class, 'delMultipleRecyclePurchases']);
    Route::post('del-multiple-recycle-expenses', [RecycleBinController::class, 'delMultipleRecycleExpenses']);
    Route::post('empty-recycle-sales', [RecycleBinController::class, 'emptyRecycleSales']);
    Route::post('empty-recycle-expenses', [RecycleBinController::class, 'emptyRecycleExpenses']);
    Route::post('empty-recycle-purchases', [RecycleBinController::class, 'emptyRecyclePurchases']);
    Route::post('delete-and-restore-multiple', [RecycleBinController::class, 'deleteAndRestoreMultipleSales']);
    Route::get('recyclebin', [RecycleBinController::class, 'index']);
    Route::get('recycle-products',[RecycleBinController::class, 'products']);
    Route::post('recycle-products',[RecycleBinController::class, 'products']);
    Route::get('recycle-product/{id}', [RecycleBinController::class, 'recycleProduct']);
    Route::post('recycle-multiple-products',[RecycleBinController::class, 'recycleMultipleProducts']);
    Route::get('del-recy-product/{id}', [RecycleBinController::class, 'delRecycleProduct']);
    Route::post('del-multiple-recycle-products', [RecycleBinController::class, 'delMultipleRecycleProducts']);
    Route::post('empty-recycle-products', [RecycleBinController::class, 'emptyRecycleProducts']);
    Route::get('recycle-sales', [RecycleBinController::class, 'sales']);
    Route::post('recycle-sales', [RecycleBinController::class, 'sales']);
    Route::get('recyclebinlive', [RecycleBinController::class, 'indexlive']); /////
    Route::get('recycle-sale/{id}', [RecycleBinController::class, 'recycleSale']);
    Route::get('recycle-purchase/{id}', [RecycleBinController::class, 'recyclePurchase']);
    Route::get('recycle-item/{id}', [RecycleBinController::class, 'recycleItem']);
    Route::get('recycle-stock/{id}', [RecycleBinController::class, 'recycleStock']);
    Route::get('recyclebinPurchase', [RecycleBinController::class, 'recyclePurchases']);
    Route::get('del-recy-sale/{id}', [RecycleBinController::class, 'delRecycleSale']);
    Route::get('del-recy-purchase/{id}', [RecycleBinController::class, 'delRecyclePurchase']);
    Route::get('del-recy-item/{id}', [RecycleBinController::class, 'delRecycleItem']);
    Route::get('del-recy-stock/{id}', [RecycleBinController::class, 'delRecycleStock']);
    Route::get('recycle-purchases', [RecycleBinController::class, 'recyclePurchases']);
    Route::post('recycle-purchases', [RecycleBinController::class, 'recyclePurchases']);
    Route::get('del-recy-expense/{id}', [RecycleBinController::class, 'delRecycleExpense']);
    Route::get('recycle-expenses',  [RecycleBinController::class, 'recycleExpenses']);
    Route::post('recycle-expenses',  [RecycleBinController::class, 'recycleExpenses']);
    Route::get('recycle-expenses/{id}',  [RecycleBinController::class, 'recycleExpensesRestore']);
    Route::get('clear-all-shop-data', [RecycleBinController::class, 'clearAllData']);
    Route::post('clear-data', [RecycleBinController::class, 'clearData']);

    Route::get('action-logs', [ActionLogsController::class, 'index']);
    Route::post('f-action-logs', [ActionLogsController::class, 'index']);
});
//notification
Route::middleware('auth')->group(function () {
    Route::get('/notifications',          [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::patch('/notifications/read-all',  [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{id}/read-redirect', [NotificationController::class, 'readAndRedirect'])
    ->name('notifications.readAndRedirect');
});

Route::post('efdms-reg-ack-infos', [ApiTestController::class, 'store']);
Route::post('efdms-rct-ack-infos', [ApiTestController::class, 'storeRctAck']);
Route::post('efdms-zreport-ack-infos', [ApiTestController::class, 'storeZReportAck']);
//Auto search
Route::get('/autocomplete-search', [RecycleBinController::class, 'autocompleteSearch']);


//
