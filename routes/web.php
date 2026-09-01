<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\StudentListController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RpsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\QrAttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalendarController;



Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Kelas
Route::resource('courses', CourseController::class);
Route::post('/courses/{course}/regenerate-key', [CourseController::class, 'regenerateKey'])
    ->name('courses.regenerate-key');

// Pertemuan
Route::get('/courses/{course}/meetings/create', [MeetingController::class, 'create'])->name('meetings.create');
Route::post('/courses/{course}/meetings', [MeetingController::class, 'store'])->name('meetings.store');
Route::get('/courses/{course}/meetings/{meeting}', [MeetingController::class, 'show'])->name('meetings.show');
Route::get('/courses/{course}/meetings/{meeting}/edit', [MeetingController::class, 'edit'])->name('meetings.edit');
Route::put('/courses/{course}/meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
Route::delete('/courses/{course}/meetings/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy');

// Materi
Route::get('/courses/{course}/meetings/{meeting}/materials/create', [MaterialController::class, 'create'])->name('materials.create');
Route::post('/courses/{course}/meetings/{meeting}/materials', [MaterialController::class, 'store'])->name('materials.store');
Route::get('/courses/{course}/meetings/{meeting}/materials/{material}/download', [MaterialController::class, 'download'])->name('materials.download');
Route::delete('/courses/{course}/meetings/{meeting}/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

// Tugas
Route::get('/courses/{course}/meetings/{meeting}/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
Route::post('/courses/{course}/meetings/{meeting}/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
Route::get('/courses/{course}/meetings/{meeting}/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
Route::delete('/courses/{course}/meetings/{meeting}/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

// Submission
Route::get('/courses/{course}/meetings/{meeting}/assignments/{assignment}/submissions/{submission}/download', [SubmissionController::class, 'download'])->name('submissions.download');
Route::get('/courses/{course}/meetings/{meeting}/assignments/{assignment}/download-all', [SubmissionController::class, 'downloadAll'])->name('submissions.download-all');
Route::delete('/courses/{course}/meetings/{meeting}/assignments/{assignment}/submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');

// Mahasiswa
Route::get('/student', [StudentController::class, 'index'])->name('student.index');
Route::post('/student/verify', [StudentController::class, 'verify'])->name('student.verify');
Route::get('/student/verify-nim/{course}', [StudentController::class, 'verifyNimForm'])->name('student.verify-nim');
Route::post('/student/verify-nim/{course}', [StudentController::class, 'verifyNim'])->name('student.verify-nim.post');
Route::get('/student/course/{course}', [StudentController::class, 'course'])->name('student.course');
Route::get('/student/course/{course}/meeting/{meeting}', [StudentController::class, 'meeting'])->name('student.meeting');Route::get('/student/course/{course}/meeting/{meeting}/assignment/{assignment}/submit', [SubmissionController::class, 'create'])->name('submissions.create');
Route::post('/student/course/{course}/meeting/{meeting}/assignment/{assignment}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
Route::get('/student/course/{course}/attendance', [StudentController::class, 'attendance'])
    ->name('student.attendance');

/*
|--------------------------------------------------------------------------
| Route untuk Absensi
|--------------------------------------------------------------------------
*/

Route::get('/courses/{course}/meetings/{meeting}/attendance', [AttendanceController::class, 'index'])
    ->name('attendances.index');
Route::post('/courses/{course}/meetings/{meeting}/attendance', [AttendanceController::class, 'store'])
    ->name('attendances.store');
Route::post('/courses/{course}/meetings/{meeting}/attendance/all-present', [AttendanceController::class, 'allPresent'])
    ->name('attendances.all-present');
Route::get('/courses/{course}/attendance/recap', [AttendanceController::class, 'recap'])
    ->name('attendances.recap');
Route::get('/courses/{course}/attendance/export', [AttendanceController::class, 'export'])
    ->name('attendances.export');
Route::get('/courses/{course}/meetings/{meeting}/attendance/print', [AttendanceController::class, 'print'])
    ->name('attendances.print');
Route::get('/courses/{course}/attendance/print-recap', [AttendanceController::class, 'printRecap'])
    ->name('attendances.print-recap');
/*
|--------------------------------------------------------------------------
| Route untuk Rekap Nilai
|--------------------------------------------------------------------------
*/

Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
Route::get('/grades/{course}', [GradeController::class, 'course'])->name('grades.course');
Route::post('/grades/{course}/components', [GradeController::class, 'storeComponent'])->name('grades.store-component');
Route::delete('/grades/{course}/components/{component}', [GradeController::class, 'destroyComponent'])->name('grades.destroy-component');
Route::post('/grades/{course}/components/{component}/grades', [GradeController::class, 'storeGrade'])->name('grades.store-grade');
Route::delete('/grades/{course}/components/{component}/grades/{grade}', [GradeController::class, 'destroyGrade'])->name('grades.destroy-grade');
Route::get('/grades/{course}/export', [GradeController::class, 'export'])->name('grades.export');
Route::get('/grades/{course}/components/manage', [GradeController::class, 'components'])->name('grades.components');
Route::get('/grades/{course}/student/{nim}', [GradeController::class, 'studentDetail'])->name('grades.student-detail');
Route::put('/grades/{course}/components/{component}/student/{nim}', [GradeController::class, 'updateGrade'])->name('grades.update-grade');
Route::post('/grades/{course}/import', [GradeController::class, 'import'])->name('grades.import');
Route::put('/grades/{course}/components/{component}', [GradeController::class, 'updateComponent'])
    ->name('grades.update-component');
/*
|--------------------------------------------------------------------------
| Route untuk Kelola Mahasiswa per Kelas
|--------------------------------------------------------------------------
*/

Route::get('/courses/{course}/students', [StudentListController::class, 'index'])
    ->name('students.index');
Route::post('/courses/{course}/students', [StudentListController::class, 'store'])
    ->name('students.store');
Route::put('/courses/{course}/students/{student}', [StudentListController::class, 'update'])
    ->name('students.update');
Route::delete('/courses/{course}/students/{student}', [StudentListController::class, 'destroy'])
    ->name('students.destroy');
Route::post('/courses/{course}/students/import', [StudentListController::class, 'import'])
    ->name('students.import');
Route::post('/courses/{course}/meetings/generate', [MeetingController::class, 'generate'])
    ->name('meetings.generate');
/*
|--------------------------------------------------------------------------
| Route untuk Cetak & PDF
|--------------------------------------------------------------------------
*/
Route::get('/grades/{course}/print', [GradeController::class, 'printGrades'])->name('grades.print');

/*
|--------------------------------------------------------------------------
| Route untuk Profil Dosen
|--------------------------------------------------------------------------
*/
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

/*
|--------------------------------------------------------------------------
| Route untuk RPS
|--------------------------------------------------------------------------
*/
Route::get('/courses/{course}/rps', [RpsController::class, 'index'])->name('rps.index');
Route::post('/courses/{course}/rps', [RpsController::class, 'store'])->name('rps.store');
Route::get('/courses/{course}/rps/{rps}/download', [RpsController::class, 'download'])->name('rps.download');
Route::delete('/courses/{course}/rps/{rps}', [RpsController::class, 'destroy'])->name('rps.destroy');
Route::get('/courses/{course}/assignments/recap', [AssignmentController::class, 'recap'])
    ->name('assignments.recap');
Route::get('/courses/{course}/assignments/print-recap', [AssignmentController::class, 'printRecap'])
    ->name('assignments.print-recap');
Route::post('/courses/{course}/meetings/{meeting}/note', [MeetingController::class, 'updateNote'])
    ->name('meetings.note');

Route::post('/submissions/{submission}/score', [SubmissionController::class, 'updateScore'])
    ->name('submissions.score');

// Export Excel
Route::get('/grades/{course}/export-excel', [GradeController::class, 'exportExcel'])->name('grades.export-excel');
Route::get('/courses/{course}/attendance/export-excel', [AttendanceController::class, 'exportExcel'])->name('attendances.export-excel');
Route::get('/courses/{course}/assignments/export-excel', [AssignmentController::class, 'exportExcel'])->name('assignments.export-excel');
Route::get('/courses/{course}/students/export-excel', [StudentListController::class, 'exportExcel'])->name('students.export-excel');
Route::post('/courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate');

Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
Route::get('/backup/download-all', [BackupController::class, 'downloadAll'])->name('backup.download-all');
Route::get('/backup/course/{course}', [BackupController::class, 'downloadCourse'])->name('backup.download-course');

// QR Code Absensi
Route::get('/courses/{course}/meetings/{meeting}/qr', [QrAttendanceController::class, 'show'])
    ->name('qr.show');
Route::post('/courses/{course}/meetings/{meeting}/qr/generate', [QrAttendanceController::class, 'generate'])
    ->name('qr.generate');
Route::get('/qr/{token}', [QrAttendanceController::class, 'scanPage'])
    ->name('qr.scan');
Route::post('/qr/{token}/process', [QrAttendanceController::class, 'process'])
    ->name('qr.process');

/*
|--------------------------------------------------------------------------
| Route untuk Admin Panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/dosens', [AdminController::class, 'dosens'])->name('admin.dosens');
    Route::get('/dosens/{user}', [AdminController::class, 'dosenDetail'])->name('admin.dosen-detail');
    Route::delete('/dosens/{user}', [AdminController::class, 'dosenDestroy'])->name('admin.dosen-destroy');
    Route::post('/dosens/{user}/storage', [AdminController::class, 'updateStorage'])->name('admin.update-storage');
});

Route::get('/grades/{course}/download-template', [GradeController::class, 'downloadTemplate'])
    ->name('grades.download-template');

Route::get('/grades/{course}/grade-letters', [GradeController::class, 'gradeLetters'])->name('grades.grade-letters');
Route::post('/grades/{course}/grade-letters', [GradeController::class, 'saveGradeLetters'])->name('grades.save-grade-letters');

Route::post('/grades/{course}/attendance-settings', [GradeController::class, 'saveAttendanceSettings'])
    ->name('grades.save-attendance-settings');

Route::post('/courses/{course}/meetings/{meeting}/assignments/{assignment}/student-score', 
    [AssignmentController::class, 'updateStudentScore'])
    ->name('assignments.student-score');

Route::get('/courses/{course}/all-assignments', [CourseController::class, 'allAssignments'])->name('courses.all-assignments');
Route::get('/courses/{course}/all-materials', [CourseController::class, 'allMaterials'])->name('courses.all-materials');

Route::post('/courses/{course}/meetings/auto-status', [MeetingController::class, 'autoUpdateStatus'])
    ->name('meetings.auto-status');
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

Auth::routes(['verify' => true]);