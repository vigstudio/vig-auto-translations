<?php

namespace VigStudio\VigAutoTranslations\Services;

use Aws\Exception\AwsException;
use Aws\Translate\TranslateClient;
use Illuminate\Support\Facades\Log;
use VigStudio\VigAutoTranslations\Contracts\Translator;

/**
 * Use the AWS Translate service
 *
 * https://aws.amazon.com/translate/
 */
class AWSTranslator implements Translator
{
    public function translate(string $source, string $target, string $value): ?string
    {
        $config = $this->loadAWSConfiguration();

        $client = new TranslateClient($config);

        try {
            $result = $client->translateText([
                'SourceLanguageCode' => $source,
                'TargetLanguageCode' => $target,
                'Text' => $value,
            ]);

            if ($result->hasKey('TranslatedText')) {
                return $result->get('TranslatedText');
            }

            return $value;
        } catch (AwsException $e) {
            if (config('plugins.vig-auto-translations.general.log_errors', true)) {
                Log::warning($e->getMessage());
            }

            return $value;
        }
    }

    /**
     * Load the configuration to pass to AWS
     */
    private function loadAWSConfiguration(): array
    {
        return [
            'version' => config('plugins.vig-auto-translations.general.aws_version', 'latest'),
            'region' => setting('vig_translate_aws_region', config('plugins.vig-auto-translations.general.aws_region')),
            'credentials' => [
                'key' => setting('vig_translate_aws_key', config('plugins.vig-auto-translations.general.aws_key')),
                'secret' => setting('vig_translate_aws_secret', config('plugins.vig-auto-translations.general.aws_secret')),
            ],
            'verify' => false,
        ];
    }
}
