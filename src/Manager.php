<?php

namespace VigStudio\VigAutoTranslations;

use Botble\Base\Facades\BaseHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;
use VigStudio\VigAutoTranslations\Contracts\Translator;
use VigStudio\VigAutoTranslations\Http\Models\VigTranslate;

class Manager
{
    protected Translator $translator;

    protected bool $withoutDatabase = false;

    public function setDriver(Translator $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function setWithoutDatabase(bool $withoutDatabase): self
    {
        $this->withoutDatabase = $withoutDatabase;

        return $this;
    }

    public function saveTranslation(string $source, string $target, string $originalValue, string $translatedValue): bool
    {
        $translation = VigTranslate::query()
            ->where('text_original', $originalValue)
            ->where('lang_from', $source)
            ->where('lang_to', $target)
            ->first();

        if (! $translation) {
            $translation = new VigTranslate();
        }

        $translation->text_original = $originalValue;
        $translation->text_translated = $translatedValue;
        $translation->lang_from = $source;
        $translation->lang_to = $target;

        return $translation->save();
    }

    public function getTranslation(string $source, string $target, string $originalValue): ?string
    {
        $translation = VigTranslate::query()
            ->where('text_original', $originalValue)
            ->where('lang_from', $source)
            ->where('lang_to', $target)
            ->first();

        if ($translation) {
            return $translation->text_translated;
        }

        return null;
    }

    public function handle(string $source, string $target, string $value): ?string
    {
        $originalValue = $value;

        $value = str_replace('%', '#_#', $value);

        $variables = $this->findVariablesByRule($value, ':');

        foreach ($variables as $item) {
            $value = str_replace($item, '%s', $value);
        }

        $translated = $this->translator->translate($source, $target, $value);

        $translated = str_replace('%S', '%s', $translated);

        if (count($this->findVariablesByRule($translated, '%s')) !== count($variables)) {
            return $originalValue;
        }

        try {
            $translated = sprintf($translated, ...$variables);
        } catch (Throwable) {
            return $originalValue;
        }

        $translated = str_replace('#_#', '%', $translated);
        $translated = str_replace('#_ #', '%', $translated);

        $translatedVariables = $this->findVariablesByRule($translated, '%s');

        if (count($translatedVariables) == count($variables)) {
            return $translated;
        }

        return $originalValue;
    }

    public function translate(string $source, string $target, string $value): ?string
    {
        $translated = app(Dictionary::class)->locale($target)->getTranslate($value);

        if ($translated) {
            return $translated;
        }

        if ($this->withoutDatabase) {
            return $this->handle($source, $target, $value);
        }

        $getTranslation = $this->getTranslation($source, $target, $value);

        if ($getTranslation) {
            return $getTranslation;
        }

        return $this->handle($source, $target, $value);
    }

    protected function findVariablesByRule(string $text, string $rule): array
    {
        return array_values(array_filter(explode(' ', $text), function ($item) use ($rule) {
            return str_replace($rule, '', $item) && Str::startsWith($item, $rule);
        }));
    }

    public function getThemeTranslations(string $locale): array
    {
        $translations = BaseHelper::getFileData($themeTranslationsFilePath = $this->getThemeTranslationPath($locale));

        ksort($translations);

        $defaultEnglishFile = theme_path(Theme::getThemeName() . '/lang/en.json');

        if ($defaultEnglishFile && ($locale !== 'en' || $defaultEnglishFile !== $themeTranslationsFilePath)) {
            $enTranslations = BaseHelper::getFileData($defaultEnglishFile);
            $translations = array_merge($enTranslations, $translations);

            $enTranslationKeys = array_keys($enTranslations);

            foreach ($translations as $key => $translation) {
                if (! in_array($key, $enTranslationKeys)) {
                    Arr::forget($translations, $key);
                }
            }
        }

        return array_combine(array_map('trim', array_keys($translations)), $translations);
    }

    public function getThemeTranslationPath(string $locale): string
    {
        $theme = Theme::getThemeName();

        $localeFilePath = $defaultLocaleFilePath = lang_path("vendor/themes/$theme/$locale.json");

        if (! File::exists($localeFilePath)) {
            $localeFilePath = lang_path("$locale.json");
        }

        if (! File::exists($localeFilePath)) {
            $localeFilePath = $defaultLocaleFilePath;

            File::ensureDirectoryExists(dirname($localeFilePath));

            $themeLangPath = theme_path("$theme/lang/$locale.json");

            if (! File::exists($themeLangPath)) {
                $themeLangPath = theme_path("$theme/lang/en.json");
            }

            File::copy($themeLangPath, $localeFilePath);
        }

        return $localeFilePath;
    }

    public function saveThemeTranslations(string $locale, array $translations): bool
    {
        ksort($translations);

        return BaseHelper::saveFileData($this->getThemeTranslationPath($locale), $translations);
    }
}
