<?php

namespace VigStudio\VigAutoTranslations\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use VigStudio\VigAutoTranslations\Contracts\Translator;

/**
 * Use OpenAI API for translation
 *
 * https://platform.openai.com/docs/api-reference/chat/create
 */
class ChatGPTTranslator implements Translator
{
    public function translate(string $source, string $target, string $value): ?string
    {
        $apiKey = setting('vig_translate_chatgpt_key', config('plugins.vig-auto-translations.general.chatgpt_key'));
        $model = setting('vig_translate_chatgpt_model', config('plugins.vig-auto-translations.general.chatgpt_model', 'gpt-3.5-turbo'));

        if (empty($apiKey)) {
            return $value;
        }

        // Map language codes to language names
        $languageNames = $this->getLanguageNames();
        $sourceLanguage = $languageNames[$source] ?? $source;
        $targetLanguage = $languageNames[$target] ?? $target;

        // Build messages for chat completion
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the given text accurately. Only return the translated text, nothing else.',
            ],
            [
                'role' => 'user',
                'content' => sprintf('Translate from %s to %s: %s', $sourceLanguage, $targetLanguage, $value),
            ],
        ];

        $response = Http::withoutVerifying()
            ->asJson()
            ->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.3,
            ]);

        if ($response->failed()) {
            return $value;
        }

        $translated = $response->json('choices.0.message.content');

        return Str::squish($translated ?: $value);
    }

    protected function getLanguageNames(): array
    {
        return [
            'af' => 'Afrikaans',
            'sq' => 'Albanian',
            'ar' => 'Arabic',
            'hy' => 'Armenian',
            'az' => 'Azerbaijani',
            'eu' => 'Basque',
            'be' => 'Belarusian',
            'bn' => 'Bengali',
            'bs' => 'Bosnian',
            'bg' => 'Bulgarian',
            'ca' => 'Catalan',
            'ceb' => 'Cebuano',
            'ny' => 'Chichewa',
            'zh' => 'Chinese',
            'co' => 'Corsican',
            'hr' => 'Croatian',
            'cs' => 'Czech',
            'da' => 'Danish',
            'nl' => 'Dutch',
            'en' => 'English',
            'eo' => 'Esperanto',
            'et' => 'Estonian',
            'tl' => 'Filipino',
            'fi' => 'Finnish',
            'fr' => 'French',
            'fy' => 'Frisian',
            'gl' => 'Galician',
            'ka' => 'Georgian',
            'de' => 'German',
            'el' => 'Greek',
            'gu' => 'Gujarati',
            'ht' => 'Haitian Creole',
            'ha' => 'Hausa',
            'haw' => 'Hawaiian',
            'he' => 'Hebrew',
            'hi' => 'Hindi',
            'hmn' => 'Hmong',
            'hu' => 'Hungarian',
            'is' => 'Icelandic',
            'ig' => 'Igbo',
            'id' => 'Indonesian',
            'ga' => 'Irish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'jw' => 'Javanese',
            'kn' => 'Kannada',
            'kk' => 'Kazakh',
            'km' => 'Khmer',
            'ko' => 'Korean',
            'ku' => 'Kurdish (Kurmanji)',
            'ky' => 'Kyrgyz',
            'lo' => 'Lao',
            'la' => 'Latin',
            'lv' => 'Latvian',
            'lt' => 'Lithuanian',
            'lb' => 'Luxembourgish',
            'mk' => 'Macedonian',
            'mg' => 'Malagasy',
            'ms' => 'Malay',
            'ml' => 'Malayalam',
            'mt' => 'Maltese',
            'mi' => 'Maori',
            'mr' => 'Marathi',
            'mn' => 'Mongolian',
            'my' => 'Myanmar (Burmese)',
            'ne' => 'Nepali',
            'no' => 'Norwegian',
            'ps' => 'Pashto',
            'fa' => 'Persian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'pa' => 'Punjabi',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sm' => 'Samoan',
            'gd' => 'Scots Gaelic',
            'sr' => 'Serbian',
            'st' => 'Sesotho',
            'sn' => 'Shona',
            'sd' => 'Sindhi',
            'si' => 'Sinhala',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'so' => 'Somali',
            'es' => 'Spanish',
            'su' => 'Sundanese',
            'sw' => 'Swahili',
            'sv' => 'Swedish',
            'tg' => 'Tajik',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            'vi' => 'Vietnamese',
            'cy' => 'Welsh',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'yo' => 'Yoruba',
            'zu' => 'Zulu',
        ];
    }
}
