<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DudiController;
use App\Http\Controllers\InternshipBatchController;
use App\Http\Controllers\InternshipBatchDetailController;
use App\Http\Controllers\InternshipReportController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MapMonitoringController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\CheckIfAuthenticated;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

// Route::middleware([\App\Http\Middleware\MaintenanceMode::class])->group(function () {
//     Route::get('/{any}', function () {
//         return view('maintenance');
//     })->where('any', '.*');
// });


Route::middleware(['web'])->group(function () {
    // Route Login (Menampilkan halaman login untuk yang belum login)
    Route::get('/login', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard'); // Jika sudah login, redirect ke dashboard
        }
        return view('auth.login'); // Menampilkan halaman login jika belum login
    })->name('loginpage')->middleware('guest'); // Middleware guest untuk hanya yang belum login

    Route::post('/login', [LoginController::class, 'login'])->name('login');

    // Route untuk halaman utama (/), mengarah ke dashboard jika sudah login
    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard'); // Redirect ke dashboard jika sudah login
        }
        return redirect()->route('login'); // Redirect ke login jika belum login
    })->middleware('guest'); // Hanya bisa diakses jika pengguna belum login

    // Route untuk dashboard, hanya bisa diakses oleh yang sudah login
    Route::get('/dashboard', function () {
        return view('dashboard'); // Halaman dashboard
    })->middleware('auth')->name('dashboard'); // Middleware auth agar hanya yang sudah login yang bisa akses

    // Grouped routes under auth middleware
    Route::middleware(['auth'])->group(function () {

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Route Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::delete('/presence/{id}', [DashboardController::class, 'delete'])->name('presence.delete');

        // Route Admin
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');
        Route::put('/admin/{id}', [AdminController::class, 'update'])->name('admin.update');
        Route::get('/admin/{id}/edit', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/admin/{id}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
        Route::get('/archive_admin', [AdminController::class, 'archives'])->name('admin.archive');

        // Route Mentor
        Route::get('/mentor', [MentorController::class, 'index'])->name('mentor');
        Route::post('/mentor', [MentorController::class, 'store'])->name('mentor.store');
        Route::get('/mentor/{id}/edit', [MentorController::class, 'edit'])->name('mentor.edit');
        Route::put('/mentor/{id}', [MentorController::class, 'update'])->name('mentor.update');
        Route::get('/archive_mentor', [MentorController::class, 'archives'])->name('mentor.archive');
        Route::post('/mentor/update-chat-id', [MentorController::class, 'updateChatId'])->name('mentor.updateChatId');

        // Route Student
        Route::get('/student', [StudentController::class, 'index'])->name('student');
        Route::post('/student', [StudentController::class, 'store'])->name('student.store');
        Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('student.edit');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('student.update');
        Route::get('/archive_student', [StudentController::class, 'archives'])->name('student.archive');

        // Route Class
        Route::get('/class', [ClassController::class, 'index'])->name('class');
        Route::post('/class', [ClassController::class, 'store'])->name('class.store');
        Route::get('/class/{id}/edit', [ClassController::class, 'edit'])->name('class.edit');
        Route::put('/class/{code}', [ClassController::class, 'update'])->name('class.update');

        // Route Dudi
        Route::get('/dudi', [DudiController::class, 'index'])->name('dudi');
        Route::post('/dudi', [DudiController::class, 'store'])->name('dudi.store');
        Route::get('/dudi/{id}/edit', [DudiController::class, 'edit'])->name('dudi.edit');
        Route::put('/dudi/{id}', [DudiController::class, 'update'])->name('dudi.update');

        // Route Department
        Route::get('/department', [DepartmentController::class, 'index'])->name('department');
        Route::post('/department', [DepartmentController::class, 'store'])->name('department.store');
        Route::get('/department/{code}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
        Route::put('/department/{code}', [DepartmentController::class, 'update'])->name('department.update');

        // Route Batch
        Route::get('/batch', [InternshipBatchController::class, 'index'])->name('batch');
        Route::post('/batch', [InternshipBatchController::class, 'store'])->name('batch.store');
        Route::get('/batch/{id}/edit', [InternshipBatchController::class, 'edit'])->name('batch.edit');
        Route::put('/batch/{id}', [InternshipBatchController::class, 'update'])->name('batch.update');
        Route::delete('/batch/{id}', [InternshipBatchController::class, 'destroy'])->name('batch.destroy');
        Route::get('/batch-dates', [InternshipBatchController::class, 'getInternshipDates']);
        Route::put('batch/{id}/update-status', [InternshipBatchController::class, 'updateStatus'])->name('batch.updateStatus');

        // Route Presences
        Route::get('/presence', [PresenceController::class, 'index'])->name('presence');
        Route::get('/history-presence', [PresenceController::class, 'history'])->name('history.presence');
        Route::get('/history-date', [PresenceController::class, 'historyDate'])->name('history.presenceDate');
        Route::get('/print-presence', [PresenceController::class, 'printPresences'])->name('print.presence');
        Route::get('/get-student-location/{student_id}', [PresenceController::class, 'getStudentLocation']);
        Route::post('/presence/store', [PresenceController::class, 'store'])->name('presence.store');
        Route::get('/history-presence/{id}/edit', [PresenceController::class, 'edit'])->name('historyPresence.edit');
        Route::put('/presence/{id}', [PresenceController::class, 'update'])->name('presence.update');
        Route::post('/presence/in', [PresenceController::class, 'checkIn'])->name('presence.checkIn');
        Route::post('/presence/out', [PresenceController::class, 'checkOut'])->name('presence.checkOut');

        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
        Route::post('/monitoring', [MonitoringController::class, 'store'])->name('monitoring.store');
        Route::delete('/monitoring/{id}', [MonitoringController::class, 'destroy'])->name('monitoring.destroy');

        Route::get('/batch-details', [InternshipBatchDetailController::class, 'index'])->name('batch-details.index');
        Route::post('/batch-details', [InternshipBatchDetailController::class, 'store'])->name('batch-details.store');
        Route::delete('/batch-details/{id}', [InternshipBatchDetailController::class, 'destroy'])->name('batch-details.destroy');
        Route::post('/batch-details/bulk-store', [InternshipBatchDetailController::class, 'bulkStore'])
            ->name('batch-details.bulk-store');
        Route::get('/batch-details/{batch}/json', [InternshipBatchDetailController::class, 'getDetailsJson'])->name('batch-details.json');
        Route::delete('/batch-details/{id}', [InternshipBatchDetailController::class, 'destroy'])->name('batch-details.destroy');

        // Route Update password
        Route::post('/update-password', [DashboardController::class, 'updatePassword'])->name('update.password');

        // Route Report
        Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
        Route::get('/v1report', [InternshipReportController::class, 'index'])->name('v1report');
        Route::get('/report', [InternshipReportController::class, 'v1index'])->name('report');
        Route::post('/report/store', [ReportController::class, 'store'])
            ->name('report.store');

        Route::get('/report/get-data', [ReportController::class, 'getReportData'])
            ->name('report.getData');

        Route::post('/report/edit', [ReportController::class, 'edit'])
            ->name('report.edit');
        Route::get('/get-report-details/{studentId}', [ReportController::class, 'getReportDetails']);
        Route::get('/grade/{studentId}', [GradeController::class, 'index'])->name('grade');
        Route::post('/save-or-update-grade', [GradeController::class, 'saveOrUpdateGrade'])->name('saveUpdateGrade');


        // Route to update an existing report (using report ID)
        Route::put('/report/{id}', [ReportController::class, 'update'])->name('report.update');

        // Route untuk cek status laporan berdasarkan report_date
        Route::get('/report-status/{date}', [ReportController::class, 'getReportForDate']);

        // Tambahkan route untuk memeriksa laporan berdasarkan tanggal
        Route::get('/check-report/{date}', [ReportController::class, 'checkReport']);

        // Define the route to get the report status for a specific date
        Route::get('/get-report-status', [ReportController::class, 'getReportStatus']);

        // Route Cetak User Siswa
        Route::get('/print-student', [StudentController::class, 'printStudents'])->name('print.student');
        // Route Cetak Mentor Siswa
        Route::get('/report-mentor', [MentorController::class, 'report'])->name('report.mentor');

        // Route lks
        Route::get('/lks', [DashboardController::class, 'lks'])->name('lks');

        // Route telegram
        Route::get('/telegram', [TelegramController::class, 'index'])->name('telegram');
        Route::post('/store-telegram', [TelegramController::class, 'store'])->name('storeTelegram');

        Route::get('/user-session', [DashboardController::class, 'userSession']);

        // Route Online
        Route::get('/online-users', [DashboardController::class, 'getOnlineUsers']);

        // Route Snappy
        // Route::get('/generate-pdf', [GradeController::class, 'generatePDF'])->name('generate.pdf');
        Route::get('/print-grade/{studentId}', [GradeController::class, 'generatePDF'])->name('generate.pdf');
        Route::get('/rekap-grade', [GradeController::class, 'rekapGrade'])->name('rekap.grade');
        Route::get('/export-rekap', [GradeController::class, 'exportRekap'])->name('export.rekap');

        // Route User
        Route::post('/activate-user/{id}', [UserController::class, 'activate'])->name('user.activate');
        Route::get('/user/{id}/archive', [UserController::class, 'archive'])->name('user.archive');
        Route::post('/reset-password/{user}', [UserController::class, 'resetPassword']);

        Route::get('/attendance-data', [DashboardController::class, 'getAttendanceData'])->name('attendance.data');

        Route::get('/presence/print', [PresenceController::class, 'print'])->name('print.presenceDate');

        Route::get('/map-monitoring', [MapMonitoringController::class, 'index'])->name('map-monitoring.index');

        Route::get('/map-monitoring/data', [MapMonitoringController::class, 'getPresencesAjax'])->name('map.monitoring.data');

        Route::get('/jurnal', [App\Http\Controllers\JurnalController::class, 'index'])->name('jurnal.index');
        Route::get('/jurnal/create', [App\Http\Controllers\JurnalController::class, 'create'])->name('jurnal.create');
        Route::post('/jurnal', [App\Http\Controllers\JurnalController::class, 'store'])->name('jurnal.store');
        Route::get('/jurnal/{jurnal}', [App\Http\Controllers\JurnalController::class, 'show'])->name('jurnal.show');
        Route::get('/jurnal/{jurnal}/edit', [App\Http\Controllers\JurnalController::class, 'edit'])->name('jurnal.edit');
        Route::put('/jurnal/{jurnal}', [App\Http\Controllers\JurnalController::class, 'update'])->name('jurnal.update');
        Route::delete('/jurnal/{jurnal}', [App\Http\Controllers\JurnalController::class, 'destroy'])->name('jurnal.destroy');
        Route::post('/jurnal/{jurnal}/sign', [App\Http\Controllers\JurnalController::class, 'sign'])->name('jurnal.sign');
    });
});
