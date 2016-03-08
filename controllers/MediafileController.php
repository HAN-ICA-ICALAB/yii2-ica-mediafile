<?php

namespace icalab\mediafile\controllers;
use Yii;
use yii\web\Controller;
use icalab\mediafile\models\Mediafiletype;
use icalab\mediafile\MediafileModule;
use icalab\mediafile\models\Mediafile;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\web\UploadedFile;
use yii\base\Exception;
use icalab\mediafile\components\ImageToPngConverter;


class MediafileController extends Controller
{

    public function actionView($id)
    {
        $model = Mediafile::findOne($id);
        if(! $model)
        {
            throw new NotFoundHttpException(Yii::t('mediafile', 'File not found.'));
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        header("Content-type: " . $model->mediafiletype->mimetype);
        echo $model->fileData;
    }

    public function actionUpdate($id)
    {
        $model = Mediafile::findOne($id);
        if(! $model)
        {
            throw new NotFoundHttpException(Yii::t('mediafile', 'File not found.'));
        }

        if(Yii::$app->request->isPost)
        {
            $model->attributes = Yii::$app->request->post('Mediafile');
            $model->file = UploadedFile::getInstance($model, 'file');
            // First figure out the mime type. We need to do this before
            // validating the model.
            if($model->file)
            {
                $mediafileType = Mediafiletype::findOne(['mimetype' => $model->file->type]);
                if($mediafileType)
                {
                    $model->mediafiletypeid = $mediafileType->id;
                }
            }
            // Save file contents to disk.
            if($model->file && $model->validate())
            {
                // Create the directory if it does not exist yet.
                $directory = preg_replace('/\/[^\/]+$/', '', $model->filePath);
                if(! file_exists($directory))
                {
                    $success = @mkdir($directory, 0755, true);
                    if(! $success)
                    {
                        Yii::error("Unable to create directory $directory");
                        throw new HttpException(500, "Unable to create directory for file upload.");
                    }
                }

                // Convert all images to PNG and save the data to disk.
                if(preg_match('/^image\//', $model->file->type))
                {
                    $converter = new ImageToPngConverter();
                    $converter->setPath($model->file->tempName);
                    $converter->setType($model->file->type);
                    $converter->convert();
                    $fileHandle = @fopen($model->filePath, 'w');
                    if(! $fileHandle)
                    {
                        throw new Exception("Unable to save file data to "
                            . $this->filePath);
                    }
                    if(false === fwrite($fileHandle, $converter->getPngData()))
                    {
                        throw new Exception("Unable to write file data to "
                            . $this->filePath);
                    }
                }
                // Not an image. Save file as is.
                else
                {

                    if(! copy($model->file->tempName, $model->filePath))
                    {
                        Yii::error("Unable to write to file " . $model->getFilePath);
                        throw new HttpException(500, "Unable to copy uploaded file to destination.");
                    }
                }

                @chmod($model->filePath, 0755);
            }

        }


        return $this->render('@icalab/mediafile/views/mediafile/update', ['model' => $model]);
    }
    public function actionCreate()
    {
        $model = new Mediafile();
        if(Yii::$app->request->isPost)
        {
            $model->attributes = Yii::$app->request->post('Mediafile');
            $model->file = UploadedFile::getInstance($model, 'file');
            // First figure out the mime type. We need to do this before
            // validating the model.
            if($model->file)
            {
                $mediafileType = Mediafiletype::findOne(['mimetype' => $model->file->type]);
                if($mediafileType)
                {
                    $model->mediafiletypeid = $mediafileType->id;
                }
            }
            // Save file contents to disk.
            if($model->file && $model->validate())
            {
                // Create the directory if it does not exist yet.
                    $directory = preg_replace('/\/[^\/]+$/', '', $model->filePath);
                    if(! file_exists($directory))
                    {
                        $success = @mkdir($directory, 0755, true);
                        if(! $success)
                        {
                            Yii::error("Unable to create directory $directory");
                            throw new HttpException(500, "Unable to create directory for file upload.");
                        }
                    }

                // Convert all images to PNG and save the data to disk.
                if(preg_match('/^image\//', $model->file->type))
                {
                    $converter = new ImageToPngConverter();
                    $converter->setPath($model->file->tempName);
                    $converter->setType($model->file->type);
                    $converter->convert();
                    $fileHandle = @fopen($model->filePath, 'w');
                    if(! $fileHandle)
                    {
                        throw new Exception("Unable to save file data to "
                            . $this->filePath);
                    }
                    if(false === fwrite($fileHandle, $converter->getPngData()))
                    {
                        throw new Exception("Unable to write file data to "
                            . $this->filePath);
                    }
                }
                // Not an image. Save file as is.
                else
                {
                    
                    if(! copy($model->file->tempName, $model->filePath))
                    {
                        Yii::error("Unable to write to file " . $model->getFilePath);
                        throw new HttpException(500, "Unable to copy uploaded file to destination.");
                    }
                }
                    
                @chmod($model->filePath, 0755);
            }

            if($model->save())
            {
                return $this->redirect(['update', 'id' => $model->primaryKey]);
            }
        }
        return $this->render('@icalab/mediafile/views/mediafile/create', ['model' => $model]);
    }
    
}
