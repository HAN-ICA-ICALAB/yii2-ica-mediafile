<?php

namespace icalab\mediafile;
use \yii\base\BootstrapInterface;
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\web\Application $app */
    public function bootstrap($app)
    {
        $app->controllerMap['mediafile'] = 'icalab\mediafile\controllers\MediafileController';
        $app->controllerMap['mediafiletype'] = 'icalab\mediafile\controllers\MediafiletypeController';
        // TODO
        /*
    public function init()
    {
        parent::init();
        $this->registerTranslations();
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

*/
    }
}
