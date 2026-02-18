<?php

namespace VigStudio\VigAutoTranslations\Contracts;

interface Translator
{
    public function translate(string $source, string $target, string $value): ?string;
}
