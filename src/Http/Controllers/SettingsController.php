<?php

namespace VigStudio\VigAutoTranslations\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use VigStudio\VigAutoTranslations\Forms\Settings\AutoTranslateSettingForm;
use VigStudio\VigAutoTranslations\Http\Requests\SettingRequest;

class SettingsController extends SettingController
{
    public function settings(Request $request): View
    {
        $this->pageTitle('Setting Vig Auto Translate');

        $form = AutoTranslateSettingForm::create();

        return view('plugins/vig-auto-translations::settings', compact('form'));
    }

    public function update(SettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
