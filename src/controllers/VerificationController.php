<?php

namespace shadowysuperstacker\nostrkit\controllers;

use craft\web\Controller;
use shadowysuperstacker\nostrkit\NostrKit;

class VerificationController extends Controller
{
    protected array|bool|int $allowAnonymous = true;
    
    public function actionAll(): array
    {
        \Craft::$app->getResponse()->format = \yii\web\Response::FORMAT_JSON;
        \Craft::$app->getResponse()->headers->add('Access-Control-Allow-Origin', '*');
        
        $cache = \Craft::$app->getCache();
        
        $requestName = \Craft::$app->getRequest()->get('name');
        
        $cacheKey = NostrKit::getInstance()->getVerifications()->getVerificationsCacheKey();
        $cacheKeyTags = NostrKit::getInstance()->getVerifications()->getVerificationsTagsCacheKey();
        
        if ($requestName) {
            $cacheKey .= '-' . $requestName;
        }
        
        return $cache->getOrSet($cacheKey, function () use ($requestName, $cacheKeyTags, $cacheKey, $cache ) {
            if ($requestName) {
                
                $names = NostrKit::getInstance()->getVerifications()->findByName($requestName);
                
                if ($cache->exists($cacheKeyTags)) {
                    $cache->set($cacheKeyTags, array_merge($cache->get($cacheKeyTags), [$cacheKey]), 0);
                } else {
                    $cache->set($cacheKeyTags, [$cacheKey], 0);
                }
            } else {
                $names = NostrKit::getInstance()->getVerifications()->getAllVerifications();
            }
            
            $names = array_map(function ($name) {
                return [
                    $name->name => $name->npub_hex
                ];
            }, $names);
            
            return [
                'names' => $names
            ];
        }, 3600 * 24 * 365);
    }
}