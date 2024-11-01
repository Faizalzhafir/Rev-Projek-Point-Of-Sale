<?php

use App\Http\Controllers\{
    DashboardController,
    CategoryController,
    LaporanController,
    ProductController,
    MemberController,
    PengeluaranController,
    PembelianController,
    PembelianDetailController,
    PenjualanController,
    PenjualanDetailController,
    SettingController,
    SupplierController,
    UserController,
    LandingController,
    LoginController,
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn () => redirect()->route('landing'));

Route::get('/landing', [LandingController::class, 'index'])->name('landing');
Route::get('landing', [LandingController::class, 'index'])->name('landing');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('login', [LoginController::class, 'index'])->name('login');

//Middleware berfungsi untuk membatasi akses setiap pengguna yang hendak masuk,apakah sudah terdaftar atau tidak
//group mengelompokan untuk route list,yang dibatasi dengan middleware,auth merupakan alias yang ada di Kernel.php,yang nantinya mengarahkan kita ke halaman yang menampilkan route untuk middlewarenya
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //level mengarah ke alias yang ada Kernel.php dan 1 merupakan level yang ada di kolom level di database,sehingga teridentifikasi level berapa saja dengan hak aksesnya
    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/category/data', [CategoryController::class, 'data'])->name('category.data');
        //route reource memiliki fungsi otomatis untuk memuat seluruh route dasar yang diperlukan,Laravel akan secara otoatis membuatnya terutama untuk kebutuhan operasi crud dan lainnya pada resource category,sehingga tidak perlu mendefinisikan setiap route
        //tetapi perlu diperhatikan pula terkait kekurangannya,akan lebih baik apabila penggunaan ini dapat kita sesuaikan tergantung kebutuhan,dengan menilai kekurangan dan kelebihannya terlebih dahulu aas sistem yang kita buat
        Route::resource('/category', CategoryController::class );
        
        Route::get('/product/data', [ProductController::class, 'data'])->name('product.data');
        Route::post('/product/delete-selected', [ProductController::class, 'deleteSelected'])->name('product.delete_selected');
        Route::post('/product/cetak-barcode', [ProductController::class, 'cetakBarcode'])->name('product.cetak_barcode');
        Route::resource('/product', ProductController::class);
    
        Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
        Route::resource('/member', MemberController::class );
    
        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::delete('supplier/{id}', [SupplierController::class, 'delete'])->name('supplier.delete');
        Route::resource('/supplier', SupplierController::class );
    
        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class );
    
        Route::get('/pembelian/{id}/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
        Route::resource('/pembelian', PembelianController::class)
            ->except('create');
    
        Route::get('/pembelian_detail/{id}/data', [PembelianDetailController::class, 'data'])->name('pembelian_detail.data');
        Route::get('/pembelian_detail/loadform/{diskon}/{total}', [PembelianDetailController::class, 'loadForm'])->name('pembelian_detail.load_form');
        Route::resource('/pembelian_detail', PembelianDetailController::class)
            ->except('create', 'show', 'edit');
    
        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}/show', [PenjualanController::class, 'show'])->name('penjualan.show');
        // Route::get('/penjualan/{id}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
        Route::put('/penjualan/{id}', [PenjualanController::class, 'update'])->name('penjualan.update');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    }); //route yang berfungsi agar mengelompokan route-route (halaman) yang akan ditampilkan ,dimana berhubungan dengan file di  middleware dan kernel

    Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
    Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
    Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
    Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
    Route::get('/transaksi/nota-besar', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');

    Route::get('/product/data', [ProductController::class, 'data'])->name('product.data');

    Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
    Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
    Route::resource('/transaksi', PenjualanDetailController::class)
        ->except( 'show');

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/pdf/{awal}/{akhir}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');
        
        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class );
    
        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });

    Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
    Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');
}); 
