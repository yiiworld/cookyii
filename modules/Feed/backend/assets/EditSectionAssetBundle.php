<?php
/**
 * EditSectionAssetBundle.php
 * @author Revin Roman
 */

namespace cookyii\modules\Feed\backend\assets;

/**
 * Class EditSectionAssetBundle
 * @package cookyii\modules\Feed\backend\assets
 */
class EditSectionAssetBundle extends \yii\web\AssetBundle
{

    public $sourcePath;

    public $css = [
        'edit.css',
    ];

    public $js = [
        'factories.js',
        'SectionResource.js',
        'SectionDetailController.js',
        'SectionEditController.js',
    ];

    public $depends = [
        'backend\assets\AppAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/_sources';

        parent::init();
    }
}