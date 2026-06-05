<?php

use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InquiryController::class, 'create'])->name('inquiries.create');
Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');

Route::get('/admin/inquiries', [InquiryController::class, 'index'])->name('admin.inquiries.index');
Route::get('/admin/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('admin.inquiries.show');
Route::put('/admin/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('admin.inquiries.update');
Route::delete('/admin/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('admin.inquiries.destroy');
