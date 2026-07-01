<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Aboneler ─────────────────────────────────────────────────────────
    Route::middleware('can:view_aboneler')->group(function () {
        Route::get('/aboneler', [\App\Http\Controllers\AbonelerController::class, 'index'])->name('aboneler.index');
        Route::get('/aboneler/sync-passive', [\App\Http\Controllers\AbonelerController::class, 'syncPassiveStatus'])->name('aboneler.sync-passive');
        Route::get('/aboneler/create', [\App\Http\Controllers\AbonelerController::class, 'create'])->name('aboneler.create');
        Route::post('/aboneler', [\App\Http\Controllers\AbonelerController::class, 'store'])->name('aboneler.store');
        Route::get('/aboneler/{id}', [\App\Http\Controllers\AbonelerController::class, 'show'])->name('aboneler.show');
        Route::get('/aboneler/{id}/edit', [\App\Http\Controllers\AbonelerController::class, 'edit'])->name('aboneler.edit');
        Route::put('/aboneler/{id}', [\App\Http\Controllers\AbonelerController::class, 'update'])->name('aboneler.update');
        Route::delete('/aboneler/{id}', [\App\Http\Controllers\AbonelerController::class, 'destroy'])->name('aboneler.destroy');
        Route::post('/aboneler/{id}/meter', [\App\Http\Controllers\AbonelerController::class, 'addMeter'])->name('aboneler.addMeter');

        Route::post('/aboneler/{id}/toggle-active', [\App\Http\Controllers\AbonelerController::class, 'toggleActive'])->name('aboneler.toggle-active');
    });

    // ── Fatura İşlemleri (Kesinleşenler) ──────────────────────────────────
    Route::middleware('can:view_kesinlesen_faturalar')->group(function () {

        // Ödenen Faturalar
        Route::get('/fatura/odenenler', [App\Http\Controllers\KesinlesenFaturaController::class, 'odenenler'])->name('fatura.odenenler');
        Route::get('/fatura/kesinlesen/{id}', [App\Http\Controllers\KesinlesenFaturaController::class, 'show'])->name('kesinlesen_fatura.show');
        Route::get('/fatura/odenenler/export/excel', [App\Http\Controllers\KesinlesenFaturaController::class, 'exportOdenenlerExcel'])->name('odenenler.export.excel');
        Route::get('/fatura/odenenler/export/pdf', [App\Http\Controllers\KesinlesenFaturaController::class, 'exportOdenenlerPDF'])->name('odenenler.export.pdf');

        // AJAX endpoints (sayfa yenilemesiz navigasyon)
        Route::get('/fatura/odenenler/ajax/donemler/{yil}', [App\Http\Controllers\KesinlesenFaturaController::class, 'ajaxDonemler'])->name('fatura.odenenler.ajax.donemler');
        Route::get('/fatura/odenenler/ajax/tablo', [App\Http\Controllers\KesinlesenFaturaController::class, 'ajaxTablo'])->name('fatura.odenenler.ajax.tablo');
        Route::get('/fatura/odenenler/ajax/export-all', [App\Http\Controllers\KesinlesenFaturaController::class, 'ajaxExportAll'])->name('fatura.odenenler.ajax.export-all');

    });

    Route::middleware('can:view_itirazlar')->group(function () {
        Route::post('/fatura/itiraz/{id}', [\App\Http\Controllers\KesinlesenFaturaController::class, 'itirazEt'])
            ->name('kesinlesen_fatura.itiraz');
        Route::post('/fatura/itiraz-iptal/{id}', [\App\Http\Controllers\KesinlesenFaturaController::class, 'itirazIptal'])
            ->name('kesinlesen_fatura.itiraz_iptal');
        Route::get('/fatura/itirazlar', [\App\Http\Controllers\KesinlesenFaturaController::class, 'itirazlar'])
            ->name('fatura.itirazlar');
        Route::post('/fatura/itiraz-kaldir/{id}', [\App\Http\Controllers\KesinlesenFaturaController::class, 'itirazKaldir'])->name('fatura.itiraz.kaldir');
    });

    Route::middleware('can:view_reaktif_faturalar')->group(function () {
        Route::get('/fatura/reaktifler', [\App\Http\Controllers\ReaktiflerController::class, 'index'])->name('reaktifler.index');
    });

    // ── Excel Yükleme ───────────────────────────────────────────────────
    Route::middleware('can:upload_faturalar')->group(function () {
        Route::get('/import', [\App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [\App\Http\Controllers\ImportController::class, 'ajaxImport'])->name('import.store');
        Route::post('/import/ajax', [\App\Http\Controllers\ImportController::class, 'ajaxImport'])->name('import.ajax');
        Route::get('/import/progress', [\App\Http\Controllers\ImportController::class, 'progress'])->name('import.progress');
        Route::get('/import/audit-totals', [\App\Http\Controllers\ImportController::class, 'auditTotals'])->name('import.audit-totals');
    });

    Route::middleware('can:view_import_logs')->group(function () {
        Route::get('/import/logs', [\App\Http\Controllers\ImportController::class, 'logs'])->name('import.logs');
        Route::delete('/import/logs/{id}', [\App\Http\Controllers\ImportController::class, 'deleteLog'])->name('import.logs.destroy');
    });

    // ── Staging / Bekleme Havuzu ────────────────────────────────────────
    Route::middleware('can:view_staging_faturalar')->group(function () {
        Route::get('/staging', [\App\Http\Controllers\ImportController::class, 'staging'])->name('staging.index');
        Route::get('/staging/{id}', [\App\Http\Controllers\ImportController::class, 'show'])->name('staging.show');
        Route::post('/staging/approve-multiple', [\App\Http\Controllers\ImportController::class, 'approveMultiple'])->name('staging.approve');
        Route::post('/staging/approve-all', [\App\Http\Controllers\ImportController::class, 'approveAll'])->name('staging.approve_all');
        Route::delete('/staging/{id}', [\App\Http\Controllers\ImportController::class, 'destroyHavuz'])->name('staging.destroy');
        Route::post('/staging/send-all', [\App\Http\Controllers\ImportController::class, 'sendAllToPayment'])->name('staging.clear');
        Route::post('/staging/send-multiple', [\App\Http\Controllers\ImportController::class, 'sendToPaymentMultiple'])->name('staging.send_multiple');
        Route::post('/staging/pend-multiple', [\App\Http\Controllers\ImportController::class, 'pendMultiple'])->name('staging.pend_multiple');
        Route::post('/staging/progress', [\App\Http\Controllers\ImportController::class, 'progress'])->name('staging.progress');
        Route::post('/staging/toggle-kontrol/{id}', [\App\Http\Controllers\ImportController::class, 'toggleKontrol'])->name('staging.toggle_kontrol');
        Route::post('/staging/itiraz/{id}', [\App\Http\Controllers\ImportController::class, 'itirazEt'])->name('staging.itiraz');
        Route::post('/staging/itiraz-iptal/{id}', [\App\Http\Controllers\ImportController::class, 'itirazIptal'])->name('staging.itiraz_iptal');
        Route::post('/staging/send-reaktifler', [\App\Http\Controllers\ImportController::class, 'sendToReaktifler'])->name('staging.send-reaktifler');

        Route::post('/staging/reaktif', [\App\Http\Controllers\ImportController::class, 'sendToReaktifler'])->name('staging.reaktif');
    });

    // ── Ayarlar & Yönetim ────────────────────────────────────────────────
    Route::middleware('can:manage_users')->prefix('admin')->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{user}/permissions', [\App\Http\Controllers\UserController::class, 'permissions'])->name('users.permissions');
        Route::put('/users/{user}/permissions', [\App\Http\Controllers\UserController::class, 'updatePermissions'])->name('users.permissions.update');

        Route::get('/logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

    });

    // ── Raporlama (Birleştirilmiş) ───────────────────────────────────────
    Route::get('/raporlar/yil-bazinda', [\App\Http\Controllers\ReportController::class, 'yearly'])->name('reports.yearly');
    Route::get('/raporlar/donem-bazinda', [\App\Http\Controllers\ReportController::class, 'periodical'])->name('reports.periodical');
    Route::get('/raporlar/detayli', [\App\Http\Controllers\ReportController::class, 'detailed'])->name('reports.detailed');
    Route::get('/raporlar/tuketim', [\App\Http\Controllers\ReportController::class, 'tuketim'])->name('reports.tuketim');
    Route::get('/raporlar/endeks', [\App\Http\Controllers\ReportController::class, 'endeks'])->name('reports.endeks');
    Route::get('/raporlar/endeks/gecmis-1-yil/{tesisat_no}', [\App\Http\Controllers\ReportController::class, 'gecmis1Yil'])->name('reports.endeks.gecmis1Yil');
    Route::get('/raporlar/anormal-faturalar', [\App\Http\Controllers\AnormalFaturaController::class, 'index'])->name('reports.anormal-faturalar');
    Route::get('/raporlar/koy-merkez', [\App\Http\Controllers\ReportController::class, 'koyMerkez'])->name('reports.koy-merkez');
    Route::get('/raporlar/ek-tuketim', [\App\Http\Controllers\ReportController::class, 'ekTuketim'])->name('reports.ek-tuketim');
    Route::get('/raporlar/ek-tuketim/son-1-yil/{tesisat_no}', [\App\Http\Controllers\ReportController::class, 'ekTuketimSon1Yil'])->name('reports.ek-tuketim.son-1-yil');
    Route::get('/raporlar/endeks/pdf-karsilastir/faturalar/{donem}', [\App\Http\Controllers\ReportController::class, 'pdfKarsilastirFaturalar'])->name('reports.endeks.pdf-karsilastir.faturalar');
    Route::get('/raporlar/endeks/pdf-karsilastir/fatura-detay/{efksId}', [\App\Http\Controllers\ReportController::class, 'pdfKarsilastirFaturaDetay'])->name('reports.endeks.pdf-karsilastir.fatura-detay');
    Route::get('/raporlar/elektrik-abone-raporlari', [\App\Http\Controllers\ReportController::class, 'elektrikAboneRaporlari'])->name('reports.elektrik-abone-raporlari');

    // ── Tesis Bilgi Sistemi ─────────────────────────────────────────────
    Route::middleware('can:view_tesis_bilgi_sistemi')->prefix('tesis-bilgi-sistemi')->name('tesis-bilgi-sistemi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TesisController::class, 'index'])->name('index');
        Route::get('/tesisler', [\App\Http\Controllers\TesisController::class, 'tesisler'])->name('tesisler');
        Route::get('/tesisler/create', [\App\Http\Controllers\TesisController::class, 'create'])->name('tesisler.create');
        Route::post('/tesisler', [\App\Http\Controllers\TesisController::class, 'store'])->name('tesisler.store');
        Route::get('/tesisler/{id}', [\App\Http\Controllers\TesisController::class, 'show'])->name('tesisler.show');
        Route::get('/tesisler/{id}/edit', [\App\Http\Controllers\TesisController::class, 'edit'])->name('tesisler.edit');
        Route::put('/tesisler/{id}', [\App\Http\Controllers\TesisController::class, 'update'])->name('tesisler.update');
        Route::delete('/tesisler/{id}', [\App\Http\Controllers\TesisController::class, 'destroy'])->name('tesisler.destroy');
        Route::get('/arizalar', [\App\Http\Controllers\TesisController::class, 'arizalar'])->name('arizalar')->middleware('can:view_arizalar');
        Route::get('/arizalar/create', [\App\Http\Controllers\TesisController::class, 'arizaCreate'])->name('arizalar.create')->middleware('can:manage_arizalar');
        Route::get('/arizalar/check-kuyu', [\App\Http\Controllers\TesisController::class, 'arizaCheckKuyu'])->name('arizalar.check-kuyu')->middleware('can:manage_arizalar');
        Route::get('/arizalar/kuyu-data', [\App\Http\Controllers\TesisController::class, 'arizaGetKuyuData'])->name('arizalar.kuyu-data')->middleware('can:manage_arizalar');
        Route::post('/arizalar', [\App\Http\Controllers\TesisController::class, 'arizaStore'])->name('arizalar.store')->middleware('can:manage_arizalar');
        Route::get('/arizalar/{id}/edit', [\App\Http\Controllers\TesisController::class, 'arizaEdit'])->name('arizalar.edit')->middleware('can:manage_arizalar');
        Route::put('/arizalar/{id}', [\App\Http\Controllers\TesisController::class, 'arizaUpdate'])->name('arizalar.update')->middleware('can:manage_arizalar');
        Route::delete('/arizalar/{id}', [\App\Http\Controllers\TesisController::class, 'arizaDestroy'])->name('arizalar.destroy')->middleware('can:manage_arizalar');
        Route::patch('/arizalar/{id}/status', [\App\Http\Controllers\TesisController::class, 'arizaUpdateStatus'])->name('arizalar.status')->middleware('can:manage_arizalar');
        Route::get('/arizalar/tesis-by-abone', [\App\Http\Controllers\TesisController::class, 'arizaTesisByAbone'])->name('arizalar.tesis-by-abone')->middleware('can:manage_arizalar');
        Route::get('/araclar', [\App\Http\Controllers\TesisController::class, 'araclar'])->name('araclar')->middleware('can:view_araclar');
        Route::get('/araclar/ekle', [\App\Http\Controllers\TesisController::class, 'aracEkle'])->name('araclar.create')->middleware('can:manage_araclar');
        Route::post('/araclar', [\App\Http\Controllers\TesisController::class, 'aracStore'])->name('araclar.store')->middleware('can:manage_araclar');
        Route::put('/araclar/{id}', [\App\Http\Controllers\TesisController::class, 'aracUpdate'])->name('araclar.update')->middleware('can:manage_araclar');
        Route::delete('/araclar/{id}', [\App\Http\Controllers\TesisController::class, 'aracDestroy'])->name('araclar.destroy')->middleware('can:manage_araclar');

        Route::get('/ariza-turleri', [\App\Http\Controllers\TesisArizaTuruController::class, 'index'])->name('ariza-turleri');
        Route::get('/ariza-turleri/create', [\App\Http\Controllers\TesisArizaTuruController::class, 'create'])->name('ariza-turleri.create');
        Route::post('/ariza-turleri', [\App\Http\Controllers\TesisArizaTuruController::class, 'store'])->name('ariza-turleri.store');
        Route::get('/ariza-turleri/{id}/edit', [\App\Http\Controllers\TesisArizaTuruController::class, 'edit'])->name('ariza-turleri.edit');
        Route::put('/ariza-turleri/{id}', [\App\Http\Controllers\TesisArizaTuruController::class, 'update'])->name('ariza-turleri.update');
        Route::delete('/ariza-turleri/{id}', [\App\Http\Controllers\TesisArizaTuruController::class, 'destroy'])->name('ariza-turleri.destroy');

        Route::get('/ekip', [\App\Http\Controllers\TesisEkipController::class, 'index'])->name('ekip');
        Route::get('/ekip/create', [\App\Http\Controllers\TesisEkipController::class, 'create'])->name('ekip.create');
        Route::post('/ekip', [\App\Http\Controllers\TesisEkipController::class, 'store'])->name('ekip.store');
        Route::get('/ekip/{id}/edit', [\App\Http\Controllers\TesisEkipController::class, 'edit'])->name('ekip.edit');
        Route::put('/ekip/{id}', [\App\Http\Controllers\TesisEkipController::class, 'update'])->name('ekip.update');
        Route::delete('/ekip/{id}', [\App\Http\Controllers\TesisEkipController::class, 'destroy'])->name('ekip.destroy');

        Route::get('/ariza-raporlari/yillik', [\App\Http\Controllers\TesisController::class, 'arizaRaporYillik'])->name('ariza-raporlari.yillik')->middleware('can:view_ariza_raporlari');
        Route::get('/ariza-raporlari/yillik/detay/{yil}/{ay}', [\App\Http\Controllers\TesisController::class, 'arizaRaporYillikDetayAjax'])->name('ariza-raporlari.yillik.detay')->middleware('can:view_ariza_raporlari');
        Route::get('/ariza-raporlari/yillik-ariza', [\App\Http\Controllers\TesisController::class, 'yillikAriza'])->name('ariza-raporlari.yillik-ariza')->middleware('can:view_ariza_raporlari');
    });

    // ── Kuyu Envanteri ───────────────────────────────────────────────────
    Route::middleware('can:view_kuyu_envanteri')->group(function () {
        Route::get('/kuyu-envanteri', [\App\Http\Controllers\KuyuEnvanterController::class, 'index'])->name('kuyu-envanteri.index');
        Route::get('/kuyu-envanteri/create', [\App\Http\Controllers\KuyuEnvanterController::class, 'create'])->name('kuyu-envanteri.create')->middleware('can:manage_kuyu_envanteri');
        Route::post('/kuyu-envanteri', [\App\Http\Controllers\KuyuEnvanterController::class, 'store'])->name('kuyu-envanteri.store')->middleware('can:manage_kuyu_envanteri');
        Route::get('/kuyu-envanteri/{kuyu}', [\App\Http\Controllers\KuyuEnvanterController::class, 'show'])->name('kuyu-envanteri.show');
        Route::get('/kuyu-envanteri/{kuyu}/edit', [\App\Http\Controllers\KuyuEnvanterController::class, 'edit'])->name('kuyu-envanteri.edit')->middleware('can:manage_kuyu_envanteri');
        Route::put('/kuyu-envanteri/{kuyu}', [\App\Http\Controllers\KuyuEnvanterController::class, 'update'])->name('kuyu-envanteri.update')->middleware('can:manage_kuyu_envanteri');
        Route::delete('/kuyu-envanteri/{kuyu}', [\App\Http\Controllers\KuyuEnvanterController::class, 'destroy'])->name('kuyu-envanteri.destroy')->middleware('can:manage_kuyu_envanteri');
    });

    // ── Yardım & Destek ──────────────────────────────────────────────────
    Route::get('/yardim', function () {
        return view('help.index');
    })->name('help.index');

    Route::post('/support/send', [\App\Http\Controllers\SupportController::class, 'store'])->name('support.store');
    Route::get('/yardim/taleplerim', [\App\Http\Controllers\SupportController::class, 'indexUser'])->name('support.user.index');
    Route::post('/support/reply/{id}', [\App\Http\Controllers\SupportController::class, 'storeReply'])->name('support.reply');

    Route::middleware('can:manage_users')->prefix('admin/destek')->name('admin.support.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SupportController::class, 'index'])->name('index');
        Route::patch('/{id}', [\App\Http\Controllers\SupportController::class, 'updateStatus'])->name('update');
    });
});

require __DIR__.'/auth.php';
