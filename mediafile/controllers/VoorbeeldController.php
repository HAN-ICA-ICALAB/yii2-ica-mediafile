<?php

namespace app\modules\mediafile\controllers;

use Yii;
use app\modules\mediafile\MediafileModule;
use yii\web\Controller;
use app\modules\mediafile\models\Mediafile;
use app\modules\mediafile\models\Voorbeeld;
use app\modules\mediafile\behaviors\ControllerWithMediafileBehavior;
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
            $model->newFile = UploadedFile::getInstance($model, 'newFile');
            if($model->validate() && (! $model->newFile || $this->saveMediaFile($model)))
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
