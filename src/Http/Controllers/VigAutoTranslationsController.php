<?php

namespace VigStudio\VigAutoTranslations\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Language;
use Botble\Translation\Manager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use VigStudio\VigAutoTranslations\Manager as AutoTranslateManager;

class VigAutoTranslationsController extends BaseController
{
    public function __construct(protected AutoTranslateManager $autoTranslateManager)
    {
    }

    public function getThemeTranslations(Request $request): View
    {
        page_title()->setTitle(trans('plugins/vig-auto-translations::vig-auto-translations.title'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable'])
            ->addScriptsDirectly('vendor/core/plugins/vig-auto-translations/js/theme-translations.js')
            ->addStylesDirectly('vendor/core/plugins/translation/css/theme-translations.css');

        $data = $this->getDataTranslations($request->input('ref_lang'));

        $translations = $data['translations'];
        $groups = $data['groups'];
        $group = $data['group'];
        $defaultLanguage = $data['defaultLanguage'];

        return view(
            'plugins/vig-auto-translations::theme-translations',
            compact('translations', 'groups', 'group', 'defaultLanguage')
        );
    }

    public function getDataTranslations(?string $refLang): array
    {
        $groups = Language::getAvailableLocales();
        $defaultLanguage = Arr::get($groups, 'en');

        if (! $refLang) {
            $group = $defaultLanguage;
        } else {
            $group = Arr::first(
                Arr::where($groups, function ($item) use ($refLang) {
                    return $item['locale'] == $refLang;
                })
            );
        }

        $translations = $this->autoTranslateManager->getThemeTranslations($group['locale']);

        return [
            'translations' => $translations,
            'groups' => $groups,
            'group' => $group,
            'defaultLanguage' => $defaultLanguage,
        ];
    }

    public function postThemeTranslations(Request $request, BaseHttpResponse $response): BaseHttpResponse
    {
        if (! File::isDirectory(lang_path())) {
            File::makeDirectory(lang_path());
        }

        if (! File::isWritable(lang_path())) {
            return $response
                ->setError()
                ->setMessage(
                    trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()])
                );
        }

        $locale = $request->input('pk');
        $name = $request->input('name');
        $value = $request->input('value');

        if ($request->input('auto') == 'true') {
            $value = $this->autoTranslateManager->translate('en', $locale, $name);
        }

        if ($locale) {
            $translations = $this->autoTranslateManager->getThemeTranslations($locale);

            if ($request->has('name') && Arr::has($translations, $request->input('name'))) {
                $translations[$request->input('name')] = $value;
            }

            $this->autoTranslateManager->saveThemeTranslations($locale, $translations);
        }

        return $response
            ->setPreviousUrl(route('vig-auto-translations.theme'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function postThemeAllTranslations(Request $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $locale = $request->input('locale');
        $data = $this->getDataTranslations($locale);
        $translations = $data['translations'];

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        foreach ($translations as $key => $translation) {
            if ($key !== $translation) {
                continue;
            }

            $translations[$key] = $this->autoTranslateManager->translate('en', $locale, $key);
        }

        $this->autoTranslateManager->saveThemeTranslations($locale, $translations);

        return $response
            ->setPreviousUrl(route('vig-auto-translations.theme'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getPluginsTranslations(Request $request): View
    {
        page_title()->setTitle(trans('plugins/translation::translation.translations'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable'])
            ->addStylesDirectly('vendor/core/plugins/translation/css/translation.css');

        $group = $request->input('group');
        $refLang = $request->input('ref_lang');

        $data = $this->getPluginsTranslationsData($group, $refLang);

        $locales = Language::getAvailableLocales();
        $groups = $this->getLang();

        return view('plugins/vig-auto-translations::plugin-translations')
            ->with('sourceTranslations', $data['sourceTranslations'])
            ->with('targetTranslations', $data['targetTranslations'])
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('ref_lang', $refLang);
    }

    public function getPluginsTranslationsData(?string $group, ?string $refLang): array
    {
        $sourceTranslations = [];
        $targetTranslations = [];

        if ($group && $refLang) {
            $sourceTranslations = $this->loadPluginTranslations($group, 'en');
            $targetTranslations = $this->loadPluginTranslations($group, $refLang);
        }

        return [
            'sourceTranslations' => $sourceTranslations,
            'targetTranslations' => $targetTranslations,
        ];
    }

    public function loadPluginTranslations(string $group, string $locale): array
    {
        $parts = explode('/', $group);

        if (count($parts) < 2) {
            return [];
        }

        $type = $parts[0]; // core, packages, or plugins
        $packageName = $parts[1];
        $filePath = $parts[2] ?? null;

        $basePath = base_path() . '/platform/' . $type . '/' . $packageName . '/resources/lang/' . $locale;

        if ($filePath) {
            $fullPath = $basePath . '/' . $filePath . '.php';
        } else {
            // Load all files in the directory
            $fullPath = $basePath;
        }

        if (! file_exists($fullPath)) {
            return [];
        }

        if (is_file($fullPath)) {
            $translations = require $fullPath;

            return Arr::dot($translations);
        }

        // Load all PHP files in directory
        $translations = [];
        $files = File::glob($fullPath . '/*.php');

        foreach ($files as $file) {
            $fileTranslations = require $file;
            $translations = array_merge($translations, Arr::dot($fileTranslations));
        }

        return $translations;
    }

    public function getLang(): array
    {
        $basePath = base_path();
        $arrayPathGet = [
            'core' => $basePath . '/platform/core',
            'packages' => $basePath . '/platform/packages',
            'plugins' => $basePath . '/platform/plugins',
        ];

        $langArray = [];

        foreach ($arrayPathGet as $key => $vendorPath) {
            if (is_dir($vendorPath)) {
                $packages = File::directories($vendorPath);
                foreach ($packages as $package) {
                    $packageName = basename($package);
                    $packagePath = $vendorPath . '/' . $packageName;

                    // Only load English files as default values
                    $langPath = $packagePath . '/resources/lang/en';
                    if (! is_dir($langPath)) {
                        continue;
                    }

                    $files = File::files($langPath);
                    foreach ($files as $file) {
                        $info = pathinfo($file);
                        $group = $info['filename'];

                        // Skip non-PHP files
                        if ($info['extension'] !== 'php') {
                            continue;
                        }

                        $filePath = $key . '/' . $packageName . '/' . $group;

                        if (! is_readable($file->getPathname())) {
                            continue;
                        }

                        $translations = require $file->getPathname();
                        $langArray[$filePath] = Arr::dot($translations);
                    }
                }
            }
        }

        return $langArray;
    }

    public function postAllPluginsTranslations(Request $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $group = $request->input('group');
        $locale = $request->input('locale') ?? $request->input('ref_lang');

        // Skip if trying to translate to English
        if ($locale === 'en') {
            return $response
                ->setError()
                ->setMessage(trans('plugins/vig-auto-translations::vig-auto-translations.cannot_translate_to_english'));
        }

        $sourceTranslations = $this->loadPluginTranslations($group, 'en');

        foreach ($sourceTranslations as $key => $value) {
            $translatedValue = $this->autoTranslateManager->translate('en', $locale, $value);
            $this->saveTranslationToFile($locale, $group, $key, $translatedValue);
        }

        return $response
            ->setMessage(trans('plugins/vig-auto-translations::vig-auto-translations.translated_successfully'));
    }

    public function postPluginsTranslations(Request $request, Manager $manager, BaseHttpResponse $response): BaseHttpResponse
    {
        $group = $request->input('group');

        if (empty($group)) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/vig-auto-translations::vig-auto-translations.please_select_group'));
        }

        $name = $request->input('name');
        $value = $request->input('value');

        logger()->info('Saving translation', [
            'group' => $group,
            'name' => $name,
            'value' => $value,
        ]);

        [$locale, $key] = explode('|', $name, 2);

        logger()->info('Parsed translation data', [
            'locale' => $locale,
            'key' => $key,
            'value' => $value,
        ]);

        // Skip if trying to translate to English
        if ($locale === 'en') {
            return $response
                ->setError()
                ->setMessage(trans('plugins/vig-auto-translations::vig-auto-translations.cannot_translate_to_english'));
        }

        if ($request->input('auto') == 'true') {
            $value = $this->autoTranslateManager->translate('en', $locale, $value);
        }

        $this->saveTranslationToFile($locale, $group, $key, $value);

        return $response
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function saveTranslationToFile(string $locale, string $group, string $key, string $value): void
    {
        try {
            // Parse the group to get the file path
            // Group format: type/packageName or type/packageName/filePath
            $parts = explode('/', $group);

            if (count($parts) < 2) {
                throw new \Exception("Invalid group format: {$group}");
            }

            $type = $parts[0]; // core, packages, or plugins
            $packageName = $parts[1];
            $filePath = $parts[2] ?? null;

            // Build the file path
            $basePath = base_path() . '/platform/' . $type . '/' . $packageName . '/resources/lang/' . $locale;

            if ($filePath) {
                $fullPath = $basePath . '/' . $filePath . '.php';
            } else {
                throw new \Exception("File path not specified for group: {$group}");
            }

            logger()->info('Saving translation to file', [
                'locale' => $locale,
                'group' => $group,
                'file_path' => $fullPath,
                'key' => $key,
                'value' => $value,
            ]);

            // Load existing translations
            if (file_exists($fullPath)) {
                $translations = require $fullPath;
            } else {
                $translations = [];
            }

            // Set the new value using dot notation
            Arr::set($translations, $key, $value);

            // Ensure directory exists
            File::ensureDirectoryExists(dirname($fullPath));

            // Export the translations to PHP
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";

            // Write to file
            File::put($fullPath, $content);

            logger()->info('Translation saved successfully', [
                'file_path' => $fullPath,
                'key' => $key,
                'value' => $value,
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to save translation', [
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function getAutoTranslate(Request $request, BaseHttpResponse $response): BaseHttpResponse
    {
        if (($locale = $request->input('locale')) &&
            ($name = $request->input('name')) &&
            in_array($locale, array_keys(Language::getAvailableLocales()))) {
            $value = $this->autoTranslateManager->translate('en', $locale, $name);

            return $response->setData([$locale => $value]);
        }

        return $response->setCode(404)->setError();
    }
}
