<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\OwnerAuthController;
use App\Http\Controllers\Auth\StoreUserAuthController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\StoreController;
use App\Http\Controllers\Owner\StoreUserController;

use App\Http\Controllers\Store\CategoryController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StoreDashboardController;
use App\Http\Controllers\Store\CustomerController;
use App\Http\Controllers\Store\SaleController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Store\ReportController;

use App\Http\Controllers\Employee\SaleController as EmployeeSaleController;
use App\Http\Controllers\Employee\CustomerController as EmployeeCustomerController;
use App\Http\Controllers\Employee\ProductController as EmployeeProductController;
use App\Http\Controllers\Employee\ReportController as EmployeeReportController;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OwnerController as AdminOwnerController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;

use App\Http\Controllers\Store\SupplierController;
use App\Http\Controllers\Store\PurchaseController;
use App\Http\Controllers\Store\SupplierPaymentController;

use App\Http\Controllers\Owner\CompareController;


// ─── Rutas públicas ────────────────────────────────────────
Route::get('/', fn() => view('landing'))->name('landing');
Route::get('/offline', fn() => view('offline'))->name('offline');
Route::get('/suscripcion-vencida', fn() => view('subscription.expired'))->name('subscription.expired');
Route::get('/registro',  [RegisterController::class, 'show'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);

// ─── Auth dueños ───────────────────────────────────────────
Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('login',   [OwnerAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [OwnerAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('logout', [OwnerAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth.owner')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('stores', StoreController::class)->except(['show']);
        Route::get('comparar', [CompareController::class, 'index'])->name('compare');
        Route::get('entrar/{store_id}', function ($store_id) {
            $store = \App\Models\Store::where('owner_id', Auth::guard('owner')->id())
                                      ->findOrFail($store_id);
            session([
                'current_store_id' => $store->id,
                'store_name'       => $store->name,
            ]);
            return redirect()->route('store.products.index');
        })->name('stores.enter');
    });
});

// ─── Módulos de tienda (dueño logueado) ────────────────────
Route::middleware(['auth.owner', 'check.subscription'])->prefix('tienda')->group(function () {

    Route::get('productos',                  [ProductController::class, 'index'])->name('store.products.index');
    Route::get('productos/crear',            [ProductController::class, 'create'])->name('store.products.create');
    Route::post('productos',                 [ProductController::class, 'store'])->name('store.products.store');
    Route::get('productos/{product}/editar', [ProductController::class, 'edit'])->name('store.products.edit');
    Route::put('productos/{product}',        [ProductController::class, 'update'])->name('store.products.update');
    Route::delete('productos/{product}',     [ProductController::class, 'destroy'])->name('store.products.destroy');
    Route::post('productos/{product}/stock', [ProductController::class, 'addStock'])->name('store.products.addStock');
    Route::get('productos/barcode',          [ProductController::class, 'findByBarcode'])->name('store.products.barcode');

    Route::get('categorias',                 [CategoryController::class, 'index'])->name('store.categories.index');
    Route::post('categorias',                [CategoryController::class, 'store'])->name('store.categories.store');
    Route::put('categorias/{category}',      [CategoryController::class, 'update'])->name('store.categories.update');
    Route::delete('categorias/{category}',   [CategoryController::class, 'destroy'])->name('store.categories.destroy');

    Route::get('clientes',                   [CustomerController::class, 'index'])->name('store.customers.index');
    Route::get('clientes/crear',             [CustomerController::class, 'create'])->name('store.customers.create');
    Route::post('clientes',                  [CustomerController::class, 'store'])->name('store.customers.store');
    Route::get('clientes/{customer}',        [CustomerController::class, 'show'])->name('store.customers.show');
    Route::get('clientes/{customer}/editar', [CustomerController::class, 'edit'])->name('store.customers.edit');
    Route::put('clientes/{customer}',        [CustomerController::class, 'update'])->name('store.customers.update');
    Route::delete('clientes/{customer}',     [CustomerController::class, 'destroy'])->name('store.customers.destroy');

    Route::get('ventas',                     [SaleController::class, 'index'])->name('store.sales.index');
    Route::get('ventas/crear',               [SaleController::class, 'create'])->name('store.sales.create');
    Route::post('ventas',                    [SaleController::class, 'store'])->name('store.sales.store');
    Route::get('ventas/{sale}',              [SaleController::class, 'show'])->name('store.sales.show');
    Route::post('ventas/{sale}/abonar',      [PaymentController::class, 'store'])->name('store.payments.store');

    Route::get('reportes',                   [ReportController::class, 'index'])->name('store.reports.index');

    Route::get('empleados',                  [StoreUserController::class, 'index'])->name('store.users.index');
    Route::get('empleados/crear',            [StoreUserController::class, 'create'])->name('store.users.create');
    Route::post('empleados',                 [StoreUserController::class, 'store'])->name('store.users.store');
    Route::get('empleados/{storeUser}/editar', [StoreUserController::class, 'edit'])->name('store.users.edit');
    Route::put('empleados/{storeUser}',      [StoreUserController::class, 'update'])->name('store.users.update');
    Route::delete('empleados/{storeUser}',   [StoreUserController::class, 'destroy'])->name('store.users.destroy');

// ── Rutas de Proveedores ──────────────────────────────────────────
Route::get('proveedores',                   [SupplierController::class, 'index'])->name('store.suppliers.index');
Route::get('proveedores/crear',             [SupplierController::class, 'create'])->name('store.suppliers.create');
Route::post('proveedores',                  [SupplierController::class, 'store'])->name('store.suppliers.store');
Route::get('proveedores/{supplier}',        [SupplierController::class, 'show'])->name('store.suppliers.show');
Route::get('proveedores/{supplier}/editar', [SupplierController::class, 'edit'])->name('store.suppliers.edit');
Route::put('proveedores/{supplier}',        [SupplierController::class, 'update'])->name('store.suppliers.update');
Route::delete('proveedores/{supplier}',     [SupplierController::class, 'destroy'])->name('store.suppliers.destroy');
Route::post('proveedores/{supplier}/abonar',[SupplierPaymentController::class, 'store'])->name('store.supplierPayments.store');
 
// ── Rutas de Compras ──────────────────────────────────────────────
Route::get('compras',                       [PurchaseController::class, 'index'])->name('store.purchases.index');
Route::get('compras/crear',                 [PurchaseController::class, 'create'])->name('store.purchases.create');
Route::post('compras',                      [PurchaseController::class, 'store'])->name('store.purchases.store');
Route::get('compras/{purchase}',            [PurchaseController::class, 'show'])->name('store.purchases.show');
 

});

// ─── Auth empleados ────────────────────────────────────────
Route::prefix('empleado')->group(function () {
    Route::get('login',   [StoreUserAuthController::class, 'showLogin'])->name('storeuser.login');
    Route::post('login',  [StoreUserAuthController::class, 'login']);
    Route::post('logout', [StoreUserAuthController::class, 'logout'])->name('storeuser.logout');

    Route::middleware('auth.storeuser')->prefix('panel')->group(function () {

        Route::middleware(['role:cajero,admin', 'check.subscription'])->group(function () {
            Route::get('ventas',                     [EmployeeSaleController::class, 'index'])->name('employee.sales.index');
            Route::get('ventas/crear',               [EmployeeSaleController::class, 'create'])->name('employee.sales.create');
            Route::post('ventas',                    [EmployeeSaleController::class, 'save'])->name('employee.sales.store');
            Route::get('ventas/{sale}',              [EmployeeSaleController::class, 'show'])->name('employee.sales.show');

            Route::get('clientes',                   [EmployeeCustomerController::class, 'index'])->name('employee.customers.index');
            Route::get('clientes/{customer}',        [EmployeeCustomerController::class, 'show'])->name('employee.customers.show');
            Route::post('clientes/{customer}/pagar', [EmployeeCustomerController::class, 'pay'])->name('employee.customers.pay');
        });

        Route::middleware(['role:inventario,admin', 'check.subscription'])->group(function () {
            Route::get('productos',                  [EmployeeProductController::class, 'index'])->name('employee.products.index');
            Route::post('productos/{product}/stock', [EmployeeProductController::class, 'addStock'])->name('employee.products.addStock');
        });

        Route::middleware(['role:admin', 'check.subscription'])->group(function () {
            Route::get('reportes',                   [EmployeeReportController::class, 'index'])->name('employee.reports.index');
        });
    });
});

// ─── Admin ─────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',   [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth.admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('owners',                 [AdminOwnerController::class, 'index'])->name('owners.index');
        Route::get('owners/create',          [AdminOwnerController::class, 'create'])->name('owners.create');
        Route::post('owners',                [AdminOwnerController::class, 'store'])->name('owners.store');
        Route::get('owners/{owner}',         [AdminOwnerController::class, 'show'])->name('owners.show');
        Route::post('owners/{owner}/toggle', [AdminOwnerController::class, 'toggleActive'])->name('owners.toggle');

        Route::get('stores',                 [AdminStoreController::class, 'index'])->name('stores.index');
        Route::post('stores/{store}/toggle', [AdminStoreController::class, 'toggleActive'])->name('stores.toggle');

        Route::get('subscriptions',                          [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/pending',                  [AdminSubscriptionController::class, 'pending'])->name('subscriptions.pending');
        Route::get('subscriptions/create',                   [AdminSubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('subscriptions',                         [AdminSubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::post('subscriptions/{subscription}/confirm',  [AdminSubscriptionController::class, 'confirm'])->name('subscriptions.confirm');
        Route::post('subscriptions/{subscription}/renew',    [AdminSubscriptionController::class, 'renew'])->name('subscriptions.renew');
        Route::post('subscriptions/{subscription}/cancel',   [AdminSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    });
});