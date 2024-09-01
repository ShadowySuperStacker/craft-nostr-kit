<?php

namespace shadowysuperstacker\nostrkit\models;

use Craft;
use craft\base\Model;

/**
 * Verification model
 */
class Verification extends Model
{
    public string $name;
    public string $npub;
    protected function defineRules(): array
    {
        return [[['name', 'npub'], 'required']];
    }
}
