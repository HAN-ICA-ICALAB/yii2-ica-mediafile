<?php

namespace icalab\mediafile\controllers;

use Yii;
use icalab\mediafile\MediafileModule;
use yii\web\Controller;
use icalab\mediafile\models\Mediafile;
use icalab\mediafile\models\Voorbeeld;
use icalab\mediafile\behaviors\ControllerWithMediafileBehavior;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class VoorbeeldController extends Controller
{
    public function behaviors()
    {
        return [
            [ 'class' => ControllerWithMediafileBehavior::className(),
                'modelClass' => Voorbeeld::className(),
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Voorbeeld::find(),
                'pagination' => [
                    'pageSize' => 20,
                ]
            ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionUpdate($id)
    {
        $model = Voorbeeld::findOne($id);

        if(Yii::$app->request->isPost)
        {
            $model->attributes = Yii::$app->request->post('Voorbeeld');
            $model->newFiles = UploadedFile::getInstance($model, 'newFiles');
            if($model->validate() && (! $model->newFiles || $this->saveMediaFiles($model)))
            {
                $model->save();
                $this->redirect(['update', 'id' => $id]);
            }
        }
        
        return $this->render('update', ['model' => $model]);
    }

    public function actionUnassign($id, $mediafile)
    {
        $this->unassignMediafile($id, $mediafile);
        $this->redirect(['update', 'id' => $id]);
    }



}
