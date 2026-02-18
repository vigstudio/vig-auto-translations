<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;
use VigStudio\VigAutoTranslations\Http\Controllers\SettingsController;
use VigStudio\VigAutoTranslations\Http\Controllers\VigAutoTranslationsController;

Route::group(['namespace' => 'VigStudio\VigAutoTranslations\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'vig-auto-translations', 'as' => 'vig-auto-translations.'], function (): void {
            Route::group(['prefix' => 'settings'], function (): void {
                Route::get('', [SettingsController::class, 'settings'])->name('settings');
                Route::put('', [SettingsController::class, 'update'])->name('settings.update');
            });

            Route::group(['prefix' => 'theme'], function (): void {
                Route::get('/', [VigAutoTranslationsController::class, 'getThemeTranslations'])->name('theme');
                Route::post('/', [VigAutoTranslationsController::class, 'postThemeTranslations'])->name('theme.post');
                Route::post('all', [VigAutoTranslationsController::class, 'postThemeAllTranslations'])->name('theme.post-all');
            });

            Route::group(['prefix' => 'plugin'], function (): void {
                Route::get('', [VigAutoTranslationsController::class, 'getPluginsTranslations'])->name('plugin');
                Route::post('', [VigAutoTranslationsController::class, 'postPluginsTranslations'])->name('plugin.post');
                Route::post('all', [VigAutoTranslationsController::class, 'postAllPluginsTranslations'])->name('plugin.all');
            });

            Route::get('auto-translate', [VigAutoTranslationsController::class, 'getAutoTranslate'])->name('auto-translate');
        });
    });
});
