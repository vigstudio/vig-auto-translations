<?php

namespace VigStudio\VigAutoTranslations\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Illuminate\Support\ServiceProvider;
use VigStudio\VigAutoTranslations\Commands\AutoTranslateCommand;
use VigStudio\VigAutoTranslations\Manager;
use VigStudio\VigAutoTranslations\Services\AWSTranslator;
use VigStudio\VigAutoTranslations\Services\ChatGPTTranslator;
use VigStudio\VigAutoTranslations\Services\GeminiTranslator;
use VigStudio\VigAutoTranslations\Services\GoogleTranslator;
use VigStudio\VigAutoTranslations\Services\OpenRouteTranslator;

class VigAutoTranslationsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('plugins/vig-auto-translations')->loadHelpers();

        $this->app->singleton(Manager::class, function () {
            $manager = new Manager();

            $driver = setting('vig_translate_driver');
            $withoutDatabase = setting('vig_translate_without_database', false);

            return match ($driver) {
                'chatgpt' => $manager->setDriver(new ChatGPTTranslator())->setWithoutDatabase($withoutDatabase),
                'gemini' => $manager->setDriver(new GeminiTranslator())->setWithoutDatabase($withoutDatabase),
                'openroute' => $manager->setDriver(new OpenRouteTranslator())->setWithoutDatabase($withoutDatabase),
                'aws' => $manager->setDriver(new AWSTranslator())->setWithoutDatabase($withoutDatabase),
                default => $manager->setDriver(new GoogleTranslator())->setWithoutDatabase($withoutDatabase),
            };
        });
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes();

        $this->commands(AutoTranslateCommand::class);

        $this->app->booted(function (): void {
            $this->app->register(HookServiceProvider::class);
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('blog')
                    ->setTitle('VigAutoTranslations')
                    ->withIcon('ti ti-language')
                    ->withDescription('Dịch tự động')
                    ->withPriority(120)
                    ->withRoute('vig-auto-translations.settings')
            );
        });

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-vig-auto-translations')
                        ->priority(80)
                        ->name('plugins/vig-auto-translations::vig-auto-translations.menu_name')
                        ->icon('ti ti-language')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-vig-auto-translations-theme')
                        ->priority(1)
                        ->parentId('cms-plugins-vig-auto-translations')
                        ->name('plugins/vig-auto-translations::vig-auto-translations.name_theme')
                        ->icon('ti ti-layout-grid')
                        ->route('vig-auto-translations.theme')
                        ->permission('vig-auto-translations.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-vig-auto-translations-plugin')
                        ->priority(2)
                        ->parentId('cms-plugins-vig-auto-translations')
                        ->name('plugins/vig-auto-translations::vig-auto-translations.name_plugin')
                        ->icon('ti ti-puzzle')
                        ->route('vig-auto-translations.plugin')
                        ->permission('vig-auto-translations.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-vig-auto-translations-settings')
                        ->priority(3)
                        ->parentId('cms-plugins-vig-auto-translations')
                        ->name('plugins/vig-auto-translations::vig-auto-translations.menu_settings')
                        ->icon('ti ti-settings')
                        ->route('vig-auto-translations.settings')
                        ->permission('vig-auto-translations.index')
                );
        });
    }
}
