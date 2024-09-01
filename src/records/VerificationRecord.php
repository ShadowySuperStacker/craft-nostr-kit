<?php

namespace shadowysuperstacker\nostrkit\records;

use craft\db\ActiveRecord;

class VerificationRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%nostrkit_verifications}}';
    }
}