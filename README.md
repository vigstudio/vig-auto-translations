# VigAutoTranslations

A powerful automatic translation plugin for [Botble CMS](https://docs.botble.com/cms) that seamlessly translates themes and plugins into multiple languages using advanced AI translation services.

<img width="1715" alt="VigAutoTranslations Screenshot" src="https://user-images.githubusercontent.com/34742453/225809634-c8501f0c-725a-42b2-8ac7-143a6aa23e77.png">

## Features

- **Multiple Translation Drivers**: Support for 5 translation services
  - Google Translate (Free, no API key required)
  - Amazon AWS Translate
  - OpenAI ChatGPT (GPT-4o, GPT-4 Turbo, GPT-3.5 Turbo, O1)
  - Google Gemini (Gemini 2.0 Flash, Gemini 1.5 Pro/Flash)
  - OpenRouter API

- **Smart Translation System**:
  - Dictionary-based caching for faster translations
  - Database storage for translation history
  - Placeholder preservation during translation
  - Automatic language detection

- **Easy Integration**:
  - Artisan command for bulk translation
  - Admin panel interface for manual translation
  - Support for both theme and plugin translations
  - JSON-based language files

- **100+ Languages Supported**: Including Vietnamese, Arabic, Korean, French, Indonesian, and more

## Requirements

- Botble CMS v7.0.0+
- PHP 8.2+
- Translation plugin must be installed

## Installation

```bash
composer require vigstudio/vig-auto-translations
php artisan cms:plugin:install vig-auto-translations
php artisan cms:plugin:activate vig-auto-translations
```

## Configuration

Navigate to **Settings > VigAutoTranslations** in the admin panel to configure:

1. Select your preferred translation driver
2. Configure API keys (if required):
   - AWS: Access Key, Secret Key, Region
   - ChatGPT: API Key, Model selection
   - Gemini: API Key, Model selection
   - OpenRouter: API Key, Model selection

## Usage

### Via Artisan Command

Translate a theme to a specific locale:

```bash
# Translate current theme to Vietnamese
php artisan vig-translate:auto vi

# Translate from a specific file
php artisan vig-translate:auto fr --path=/path/to/theme/lang/en.json
```

### Via Admin Panel

1. Go to **VigAutoTranslations > Theme** in the menu
2. Select the target language
3. Click "Translate All" to auto-translate all strings

## Translation Services Comparison

| Service | Free | API Key Required | Quality | Speed |
|---------|------|------------------|---------|-------|
| Google Translate | Yes | No | Good | Fast |
| AWS Translate | No | Yes | Excellent | Fast |
| ChatGPT | No | Yes | Excellent | Medium |
| Gemini | No | Yes | Excellent | Fast |
| OpenRouter | No | Yes | Varies | Medium |

## Dictionary System

The plugin includes pre-built dictionaries for common languages:
- Arabic (`ar.json`)
- Korean (`ko.json`)
- Vietnamese (`vi.json`)

Dictionaries are automatically loaded for faster translation and can be extended with custom entries.

## Database Storage

Translations are stored in the `vig_translations` table for:
- Quick retrieval of previously translated content
- Translation history tracking
- Reducing API calls for repeated translations

## How It Works

1. **Dictionary Lookup**: First checks if translation exists in local dictionary
2. **Database Cache**: Then checks if translation was previously saved
3. **API Translation**: Finally uses the configured translation driver
4. **Smart Preservation**: Preserves placeholders like `:attribute`, `%s` during translation

## Translation Workflow

```php
// The translation process follows this order:
1. Dictionary (local JSON) → Instant, no API call
2. Database (vig_translations table) → Instant, no API call
3. Translation Driver (API) → New translation, saved to database
```

## Supported Languages

Afrikaans, Albanian, Arabic, Armenian, Azerbaijani, Basque, Belarusian, Bengali, Bosnian, Bulgarian, Catalan, Cebuano, Chichewa, Chinese, Corsican, Croatian, Czech, Danish, Dutch, English, Esperanto, Estonian, Filipino, Finnish, French, Frisian, Galician, Georgian, German, Greek, Gujarati, Haitian Creole, Hausa, Hawaiian, Hebrew, Hindi, Hmong, Hungarian, Icelandic, Igbo, Indonesian, Irish, Italian, Japanese, Javanese, Kannada, Kazakh, Khmer, Korean, Kurdish (Kurmanji), Kyrgyz, Lao, Latin, Latvian, Lithuanian, Luxembourgish, Macedonian, Malagasy, Malay, Malayalam, Maltese, Maori, Marathi, Mongolian, Myanmar (Burmese), Nepali, Norwegian, Pashto, Persian, Polish, Portuguese, Punjabi, Romanian, Russian, Samoan, Scots Gaelic, Serbian, Sesotho, Shona, Sindhi, Sinhala, Slovak, Slovenian, Somali, Spanish, Sundanese, Swahili, Swedish, Tajik, Tamil, Telugu, Thai, Turkish, Ukrainian, Urdu, Uzbek, Vietnamese, Welsh, Xhosa, Yiddish, Yoruba, Zulu

## Roadmap

- [ ] Add support for DeepL API
- [ ] Batch translation for multiple locales
- [ ] Translation quality scoring
- [ ] Auto-detect new translation keys
- [ ] Export/import translation files

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This plugin is open-sourced software licensed under the [MIT license](LICENSE).

## Author

- **VigStudio** - [Nghĩa Nè](https://nghiane.com)

## Support

For issues and questions, please visit [GitHub Issues](https://github.com/vigstudio/vig-auto-translations/issues).
