<?php

namespace shadowysuperstacker\nostrkit;

use Craft;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\web\twig\variables\Cp;
use craft\services\UserPermissions;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterUserPermissionsEvent;
use shadowysuperstacker\nostrkit\models\Settings;
use shadowysuperstacker\nostrkit\services\VerificationsService;

/**
 * Nostr Kit plugin
 *
 * @method static NostrKit getInstance()
 * @method Settings getSettings()
 * @author Shadowy Super Stacker
 * @copyright Shadowy Super Stacker
 * @license MIT
 */
class NostrKit extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = false;

    public static function config(): array
    {
        return [
            'components' => [
                'verifications' => VerificationsService::class
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        $this->attachEventHandlers();
        
        Craft::$app->onInit(function() {
            // ...
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('nostr-kit/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['.well-known/nostr.json'] = 'nostr-kit/verification/all';
        });
        
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                if (\Craft::$app->user->checkPermission('canManageVerifications')) {
                    $event->rules['nostr-kit/verifications'] = 'nostr-kit/manage-verifications/all';
                    $event->rules['nostr-kit/verifications/create'] = 'nostr-kit/manage-verifications/create';
                    $event->rules['nostr-kit/verifications/delete/<id:\d+>'] = 'nostr-kit/manage-verifications/delete';
                    $event->rules['nostr-kit/verifications/update/<id:\d+>'] = 'nostr-kit/manage-verifications/update';
                }
                
            }
        );
        
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {
                if (\Craft::$app->user->checkPermission('canManageVerifications')) {
                    $event->navItems[] = [
                        'url' => 'nostr-kit/verifications',
                        'label' => 'Nostr kit',
                        'icon' => 'check',
                        'subnav' => [
                            'verifications' => [
                                'url' => 'nostr-kit/verifications',
                                'label' => 'NIP-05 Verifications',
                                'icon' => 'check',
                            ]
                        ]
                    ];
                }
                
            }
        );
        
        \yii\base\Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function (RegisterUserPermissionsEvent $event) {
                $event->permissions['nostr-kit'] = [
                    'heading' => 'Nostr Kit',
                    'permissions' => [
                        'canManageVerifications' => [
                            'label' => 'Manage NIP-05 Verifications',
                        ],
                    ],
                ];
            }
        );
    }
    
    public function getVerifications(): VerificationsService
    {
        return $this->get('verifications');
    }
}
