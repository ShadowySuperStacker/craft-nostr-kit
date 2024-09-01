<?php

namespace shadowysuperstacker\nostrkit\controllers;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use shadowysuperstacker\nostrkit\models\Verification;
use shadowysuperstacker\nostrkit\NostrKit;
use shadowysuperstacker\nostrkit\records\VerificationRecord;

class ManageVerificationsController extends Controller
{
    public function actionAll()
    {
        $this->requirePermission('canManageVerifications');
        
        $verifications = NostrKit::getInstance()->getVerifications()->getAllVerifications();
        
        return $this->renderTemplate('nostr-kit/verifications/all', [
            'verifications' => $verifications,
            'verification' => new Verification()
        ]);
    }
    
    public function actionCreate()
    {
        $this->requirePermission('canManageVerifications');
        $verification = new Verification();
        
        $verification->name = $this->request->getBodyParam('name');
        $verification->npub = $this->request->getBodyParam('npub');
        
        if ($verification->validate()) {
            if (NostrKit::getInstance()->getVerifications()->saveVerification($verification)) {
                $this->setSuccessFlash(Craft::t('nostr-kit', 'Verification created.'));
                
                NostrKit::getInstance()->getVerifications()->deleteVerificationsCache();
                
            } else {
                $this->setFailFlash(Craft::t('nostr-kit', 'Unable to create verification.'));
            }
        }
        
        
    }
    
    public function actionDelete(int $id)
    {
        if (VerificationRecord::find()->where(['id'=>$id])->one()->delete()) {
            $this->setSuccessFlash(Craft::t('nostr-kit', 'Verification deleted.'));
            
            NostrKit::getInstance()->getVerifications()->deleteVerificationsCache();
            
        } else {
            $this->setFailFlash(Craft::t('nostr-kit', 'Unable to delete verification.'));
        }
        
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('nostr-kit/verifications'));
    }
    
}