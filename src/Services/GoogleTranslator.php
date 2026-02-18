<?php

namespace VigStudio\VigAutoTranslations\Services;

use VigStudio\VigAutoTranslations\Contracts\Translator;

class GoogleTranslator implements Translator
{
    public function translate(string $source, string $target, string $value): ?string
    {
        return (new GoogleTranslate())
            ->setSource($source)
            ->setTarget($target)
            ->translate($value);
    }
}
