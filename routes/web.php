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
        Route::post('/aboneler/mark-selected-old', [\App\Http\Controllers\AbonelerController::class, 'markSelectedOld'])->name('aboneler.mark-selected-old');
        Route::post('/aboneler/mark-all-old', [\App\Http\Controllers\AbonelerController::class, 'markAllOld'])->name('aboneler.mark-all-old');
        Route::get('/aboneler/{id}', [\App\Http\Controllers\AbonelerController::class, 'show'])->name('aboneler.show');
        Route::get('/aboneler/{id}/edit', [\App\Http\Controllers\AbonelerController::class, 'edit'])->name('aboneler.edit');
        Route::put('/aboneler/{id}', [\App\Http\Controllers\AbonelerController::class, 'update'])->name('aboneler.update');
        Route::delete('/aboneler/{id}', [\App\Http\Controllers\AbonelerController::class, 'destroy'])->name('aboneler.destroy');
        Route::post('/aboneler/{id}/meter', [\App\Http\Controllers\AbonelerController::class, 'addMeter'])->name('aboneler.addMeter');
        Route::post('/aboneler/{id}/mark-old', [\App\Http\Controllers\AbonelerController::class, 'markOld'])->name('aboneler.mark-old');
        Route::post('/aboneler/{id}/toggle-active', [\App\Http\Controllers\AbonelerController::class, 'toggleActive'])->name('aboneler.toggle-active');
    });

    // ── Fatura İşlemleri (Kesinleşenler) ──────────────────────────────────
    Route::middleware('can:view_kesinlesen_faturalar')->group(function () {

        // Ödenen Faturalar
        Route::get('/fatura/odenenler', [App\Http\Controllers\KesinlesenFaturaController::class, 'odenenler'])->name('fatura.odenenler');
        Route::post('/fatura/odenenler/analiz', [App\Http\Controllers\KesinlesenFaturaController::class, 'analizEt'])->name('fatura.odenenler.analiz');
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

        // Bölge Yönetimi
        Route::get('/bolgeler', [\App\Http\Controllers\BolgelerController::class, 'index'])->name('bolgeler.index');
        Route::post('/bolgeler', [\App\Http\Controllers\BolgelerController::class, 'store'])->name('bolgeler.store');
        Route::put('/bolgeler/{id}', [\App\Http\Controllers\BolgelerController::class, 'update'])->name('bolgeler.update');
    });

    // ── Raporlama (Birleştirilmiş) ───────────────────────────────────────
    Route::get('/raporlar/yil-bazinda', [\App\Http\Controllers\ReportController::class, 'yearly'])->name('reports.yearly');
    Route::get('/raporlar/donem-bazinda', [\App\Http\Controllers\ReportController::class, 'periodical'])->name('reports.periodical');
    Route::get('/raporlar/detayli', [\App\Http\Controllers\ReportController::class, 'detailed'])->name('reports.detailed');
    Route::get('/raporlar/endeks', [\App\Http\Controllers\ReportController::class, 'endeks'])->name('reports.endeks');
    Route::get('/raporlar/endeks/gecmis-6-ay/{tesisat_no}', [\App\Http\Controllers\ReportController::class, 'gecmis6Ay'])->name('reports.endeks.gecmis6Ay');
    Route::get('/raporlar/anormal-faturalar', [\App\Http\Controllers\AnormalFaturaController::class, 'index'])->name('reports.anormal-faturalar');
    Route::get('/raporlar/koy-merkez', [\App\Http\Controllers\ReportController::class, 'koyMerkez'])->name('reports.koy-merkez');

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
