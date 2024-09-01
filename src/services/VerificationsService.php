<?php

namespace shadowysuperstacker\nostrkit\services;

use Craft;
use swentel\nostr\Key\Key;
use shadowysuperstacker\nostrkit\records\VerificationRecord;

class VerificationsService
{
    public function getAllVerifications(): array
    {
        return VerificationRecord::find()->all();
    }
    
    public function saveVerification(\shadowysuperstacker\nostrkit\models\Verification $verification): bool
    {
        $key = new Key();
        $record = new VerificationRecord();
        
        $record->name = $verification->name;
        $record->npub = $verification->npub;
        $record->npub_hex = $key->convertToHex($verification->npub);
        
        return $record->save();
    }
    
    public function findByName(string $name): array
    {
        return VerificationRecord::find()->where(['name' => $name])->all();
    }
    
    public function getVerificationsCacheKey(): string
    {
        return 'nostrKit-verifications';
    }
    
    public function getVerificationsTagsCacheKey(): string
    {
        return 'nostrKit-verifications-tags';
    }
    
    public function deleteVerificationsCache(): void
    {
        Craft::$app->getCache()->delete($this->getVerificationsCacheKey());
        $cacheTags = Craft::$app->getCache()->get($this->getVerificationsTagsCacheKey());

        if($cacheTags) {
            foreach ($cacheTags as $tag) {
                Craft::$app->getCache()->delete($tag);
            }
        }   
    }
}