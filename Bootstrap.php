<?php

namespace icalab\mediafile;
use \yii\base\BootstrapInterface;
use \Yii;
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\web\Application $app */
    public function bootstrap($app)
    {
        $app->controllerMap['mediafile'] = 'icalab\mediafile\controllers\MediafileController';
        $app->controllerMap['mediafiletype'] = 'icalab\mediafile\controllers\MediafiletypeController';
        Yii::$app->i18n->translations['mediafile*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@icalab/mediafile/messages',
        ];
    }
}
