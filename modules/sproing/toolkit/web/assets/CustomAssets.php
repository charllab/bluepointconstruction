<?php
namespace modules\sproing\toolkit\web\assets;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Class CustomAssets
 * @since 2.1.0
 */
class CustomAssets extends AssetBundle
{

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->sourcePath = '@sproing/toolkit/resources';

        $this->depends = [CpAsset::class];

        $this->css = ['redactor/styles.css'];
    }
}
