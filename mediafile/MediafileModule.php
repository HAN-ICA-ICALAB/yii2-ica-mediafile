<?php
namespace app\modules\mediafile;
use Yii;
class MediafileModule extends \yii\base\Module
{
    public function init()
    {
        parent::init();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/mediafile/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/modules/mediafile/messages',
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/mediafile/' . $category, $message, $params, $language);
    }
}
