<?php

namespace VigStudio\VigAutoTranslations\Forms\Settings;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use VigStudio\VigAutoTranslations\Http\Requests\SettingRequest;

class AutoTranslateSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScriptsDirectly('vendor/core/plugins/vig-auto-translations/js/settings.js');

        $formId = 'vig-auto-translations-settings';
        $currentDriver = setting('vig_translate_driver', 'google');

        $this
            ->setSectionTitle(trans('plugins/vig-auto-translations::vig-auto-translations.title'))
            ->setSectionDescription(trans('plugins/vig-auto-translations::vig-auto-translations.description'))
            ->setFormOption('id', $formId)
            ->setValidatorClass(SettingRequest::class)
            ->setActionButtons(view('core/setting::forms.partials.action', ['form' => $formId])->render())
            ->add(
                'vig_translate_driver',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.setting_driver'))
                    ->choices([
                        'google' => trans('plugins/vig-auto-translations::vig-auto-translations.google'),
                        'aws' => trans('plugins/vig-auto-translations::vig-auto-translations.aws'),
                        'chatgpt' => trans('plugins/vig-auto-translations::vig-auto-translations.openai'),
                        'gemini' => trans('plugins/vig-auto-translations::vig-auto-translations.gemini'),
                        'openroute' => trans('plugins/vig-auto-translations::vig-auto-translations.openroute'),
                    ])
                    ->selected($currentDriver)
                    ->toArray()
            )
            ->add(
                'vig_translate_without_database',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.without_database'))
                    ->defaultValue(old('vig_translate_without_database', setting('vig_translate_without_database', false)))
                    ->toArray()
            )
            // AWS Settings
            ->add(
                'vig_translate_aws_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.aws_key'))
                    ->maxLength(120)
                    ->allowOverLimit()
                    ->value(old('vig_translate_aws_key', setting('vig_translate_aws_key', config('plugins.vig-auto-translations.general.aws_key'))))
                    ->cssClass('form-control vig-translate-aws-field')
                    ->toArray()
            )
            ->add(
                'vig_translate_aws_secret',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.aws_secret'))
                    ->maxLength(120)
                    ->allowOverLimit()
                    ->value(old('vig_translate_aws_secret', setting('vig_translate_aws_secret', config('plugins.vig-auto-translations.general.aws_secret'))))
                    ->cssClass('form-control vig-translate-aws-field')
                    ->toArray()
            )
            ->add(
                'vig_translate_aws_region',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.aws_region'))
                    ->maxLength(120)
                    ->allowOverLimit()
                    ->value(old('vig_translate_aws_region', setting('vig_translate_aws_region', config('plugins.vig-auto-translations.general.aws_region'))))
                    ->cssClass('form-control vig-translate-aws-field')
                    ->toArray()
            )
            // OpenAI/ChatGPT Settings
            ->add(
                'vig_translate_chatgpt_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.openai_key'))
                    ->maxLength(120)
                    ->allowOverLimit()
                    ->value(old('vig_translate_chatgpt_key', setting('vig_translate_chatgpt_key', config('plugins.vig-auto-translations.general.chatgpt_key'))))
                    ->cssClass('form-control vig-translate-chatgpt-field')
                    ->toArray()
            )
            ->add(
                'vig_translate_chatgpt_model',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.openai_model'))
                    ->choices([
                        'gpt-4o' => 'GPT-4o (Latest)',
                        'gpt-4o-mini' => 'GPT-4o Mini',
                        'gpt-4-turbo' => 'GPT-4 Turbo',
                        'gpt-4' => 'GPT-4',
                        'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                        'o1-preview' => 'o1-preview',
                        'o1-mini' => 'o1-mini',
                    ])
                    ->selected(old('vig_translate_chatgpt_model', setting('vig_translate_chatgpt_model', config('plugins.vig-auto-translations.general.chatgpt_model', 'gpt-3.5-turbo'))))
                    ->cssClass('form-control vig-translate-chatgpt-field')
                    ->toArray()
            )
            // Gemini Settings
            ->add(
                'vig_translate_gemini_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.gemini_key'))
                    ->maxLength(120)
                    ->allowOverLimit()
                    ->value(old('vig_translate_gemini_key', setting('vig_translate_gemini_key', config('plugins.vig-auto-translations.general.gemini_key'))))
                    ->cssClass('form-control vig-translate-gemini-field')
                    ->toArray()
            )
            ->add(
                'vig_translate_gemini_model',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.gemini_model'))
                    ->choices([
                        'gemini-2.0-flash-exp' => 'Gemini 2.0 Flash Exp',
                        'gemini-exp' => 'Gemini Exp',
                        'gemini-1.5-pro' => 'Gemini 1.5 Pro',
                        'gemini-1.5-flash' => 'Gemini 1.5 Flash',
                        'gemini-1.0-pro' => 'Gemini 1.0 Pro',
                    ])
                    ->selected(old('vig_translate_gemini_model', setting('vig_translate_gemini_model', config('plugins.vig-auto-translations.general.gemini_model', 'gemini-pro'))))
                    ->cssClass('form-control vig-translate-gemini-field')
                    ->toArray()
            )
            // OpenRoute Settings
            ->add(
                'vig_translate_openroute_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.openroute_key'))
                    ->maxLength(120)
                    ->allowOverLimit()
                    ->value(old('vig_translate_openroute_key', setting('vig_translate_openroute_key', config('plugins.vig-auto-translations.general.openroute_key'))))
                    ->cssClass('form-control vig-translate-openroute-field')
                    ->toArray()
            )
            ->add(
                'vig_translate_openroute_model',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/vig-auto-translations::vig-auto-translations.openroute_model'))
                    ->placeholder('e.g., openai/gpt-4o, anthropic/claude-3-haiku, google/gemini-pro')
                    ->maxLength(200)
                    ->allowOverLimit()
                    ->value(old('vig_translate_openroute_model', setting('vig_translate_openroute_model', config('plugins.vig-auto-translations.general.openroute_model'))))
                    ->cssClass('form-control vig-translate-openroute-field')
                    ->helperText(trans('plugins/vig-auto-translations::vig-auto-translations.openroute_model_help'))
                    ->toArray()
            );
    }
}
