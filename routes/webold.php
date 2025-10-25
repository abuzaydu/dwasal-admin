<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\Site\CarouselController;
use App\Http\Controllers\Site\GeneralServiceController;
use App\Http\Controllers\Site\FaqController;
use App\Http\Controllers\Site\TestimonialController;
use App\Http\Controllers\Site\TeamController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\AboutItemController;


use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\CompanyController;
use App\Http\Controllers\Settings\CompanyRoleController;
use App\Http\Controllers\Settings\ShopController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\DeliveryRateController;
use App\Http\Controllers\Settings\UnitEquivalentController;

// Production
use App\Http\Controllers\SandProd\DashboardController;
use App\Http\Controllers\SandProd\WashingPlantController;
use App\Http\Controllers\SandProd\WashingEquipmentController;
use App\Http\Controllers\SandProd\StorageLocationController;
use App\Http\Controllers\SandProd\MaintenanceRecordController;
use App\Http\Controllers\SandProd\RawMaterialSourceController;
use App\Http\Controllers\SandProd\RMSourcingController;
use App\Http\Controllers\SandProd\ProductionRunController;
use App\Http\Controllers\SandProd\QualityTestController;

use App\Http\Controllers\Shop\VehicleController;
use App\Http\Controllers\Shop\ServiceController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\ProductUnitController;
use App\Http\Controllers\Shop\ProductImageController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\OrderPaymentController;
use App\Http\Controllers\Shop\OrderDeliveryController;

use App\Http\Controllers\Shop\QuoteRequestController;
use App\Http\Controllers\Shop\QuotationController;

use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\DashController;

Route::get('/', [WelcomeController::class, 'index']);
Auth::routes();

Route::group(['middleware' => 'auth'], function () {
	
	
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
    Route::get('users-and-roles', [ProfileController::class, 'usersAndRoles']);
    Route::post('users-and-roles', [ProfileController::class, 'usersAndRoles']);
    Route::get('change-password', [ProfileController::class, 'changePassForm']);
    Route::post('change-password', [ProfileController::class, 'changePass']);
    Route::post('change-theme', [ProfileController::class, 'changeTheme']);
    Route::resource('user-companies', CompanyController::class);
    Route::post('switch-company', [CompanyController::class, 'switchCompany']);
    Route::resource('company-roles', CompanyRoleController::class);
    Route::post('assign-business', [ProfileController::class, 'assignBusiness']);
    Route::post('detach-business', [ProfileController::class, 'detachBusiness']);
    Route::get('activate-user/{id}', [ProfileController::class, 'activateUser']);
    Route::get('remove-user/{id}', [ProfileController::class, 'removeUser']);
    Route::post('change-user-role', [ProfileController::class, 'assignUserRole']);


    Route::post('update-bsettings', [SettingsController::class, 'store']);
    Route::resource('settings', SettingsController::class);
    Route::get('invoice-template-settings', [SettingsController::class, 'invoiceTemplateSetting']);
    Route::get('daily-closing-time-settings', [SettingsController::class, 'dctSettings']);
    Route::post('edit-settings', [SettingsController::class, 'update']);
    Route::post('change-btype', [SettingsController::class, 'show']);
    Route::post('set-currency', [SettingsController::class, 'setCurrency']);
    Route::get('rem-currency/{id}', [SettingsController::class, 'removeCurrency']);
    Route::get('make-default-currency/{id}', [SettingsController::class, 'makeDefaultCurrency']);
    
	//Production Routitn
	Route::get('sand-prod-dash', [DashboardController::class, 'index']);
	Route::post('sand-prod-dash', [DashboardController::class, 'index']);

	Route::resource('washing-plants', WashingPlantController::class);
	Route::resource('washing-equipments', WashingEquipmentController::class);
	Route::resource('maintenance-records', MaintenanceRecordController::class);
	Route::resource('storage-locations', StorageLocationController::class);

	Route::resource('raw-material-sources', RawMaterialSourceController::class);
	Route::resource('rm-sourcings', RMSourcingController::class);
	Route::post('f-rm-sourcings', [RMSourcingController::class, 'index']);

	Route::resource('sand-productions', ProductionRunController::class);
	Route::resource('quality-tests', QualityTestController::class);


	Route::get('/home', [HomeController::class, 'index'])->name('home');
	Route::get('sales-dash', [HomeController::class, 'revDashboard']);

	Route::resource('services', ServiceController::class);
	Route::resource('categories', CategoryController::class);
	Route::post('f-categories', [CategoryController::class, 'index']);
	Route::resource('products', ProductController::class);
	Route::post('f-products', [ProductController::class, 'index']);
	Route::get('create-basic-unit/{id}', [ProductController::class, 'createBasicUnit']);
	Route::resource('product-units', ProductUnitController::class);
	Route::resource('product-images', ProductImageController::class);

	Route::resource('quote-requests', QuoteRequestController::class);
	Route::resource('quotations', QuotationController::class);
	//OrdersProcessing
	Route::resource('orders', OrderController::class);
	Route::post('f-orders', [OrderController::class, 'index']);
	Route::resource('order-deliveries', OrderDeliveryController::class);
	Route::post('f-order-deliveries', [OrderDeliveryController::class, 'index']);
	Route::post('add-selected-item', [OrderDeliveryController::class, 'addDeliveryItem']);
	Route::post('update-delivery-item', [OrderDeliveryController::class, 'updateDeliveryItem']);
	Route::post('update-order-status', [OrderController::class, 'updateOrderStatus']);
	Route::resource('order-payments', OrderPaymentController::class);
	Route::post('card-payments', [OrderPaymentController::class, 'initiateOrder']);
	Route::post('check-payment-order-status', [OrderPaymentController::class, 'checkOrderStatus']);

	Route::resource('vehicles', VehicleController::class);

	// Client Route
	Route::get('my-dashboard', [DashController::class, 'index']);
	Route::resource('my-orders', CheckoutController::class);
	Route::get('order-tracking/{id}', [CheckoutController::class, 'orderTracking']);
});
