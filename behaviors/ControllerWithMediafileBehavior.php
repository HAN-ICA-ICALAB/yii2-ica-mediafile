<?php
namespace icalab\mediafile\behaviors;

/*****************************************************************************
 *                                                                           *
 * Behavior for adding useful methods for working with models with attached  *
 * mediafiles (models that have the ModelWithMediafileBehavior behavior).    *
 *                                                                           *
 ****************************************************************************/

use Yii;
use yii\base\Exception;
use yii\base\Behavior;
use icalab\mediafile\models\Mediafile;
use icalab\mediafile\models\Mediafiletype;
use icalab\mediafile\components\ImageToPngConverter;
use yii\web\HttpException;
class ControllerWithMediafileBehavior extends Behavior
{
    public $modelClass;

    /**
     * Function that unassigns a media file from an object. Since it is not 
     * possible to put a controller in an action, you have to wrap this
     * function in one of your own actions.
     * @param id the id of the parent model
     * @param mediafile the id of the media file
     */
    public function unassignMediafile($id, $mediafile)
    {
        $class = $this->modelClass;
        $model = $class::findOne($id);
        if(null === $model)
        {
            throw new NotFoundHttpException(MediafileModule::t('mediafile', 'No such model.'));
        }


        $mediafile = Mediafile::findOne($mediafile);
        if(null !== $mediafile)
        {
            $model->removeMediafile($mediafile);
        }
    }

    /**
     * Save a new media file and attach it to a specific model. The newFile 
     * attribute of the model must be set tp an instance of UploadedFile.
     * 
     * Example:
     * $model = new SomeModel();
     * $model->newFile = UploadedFile::getInstance($model, 'newFile');
     * if($model->newFile)
     * {
     *   $this->saveMediafile($model);
     * }
     *
     * @param model the model to attach the file to. The newFile attribute of 
     * the model must be set, otherwise nothing happens
     * @param saveAsPng if true, images will be saved as png (this is the 
     * default)
     * @return whether saving succeeded or not
     */
    public function saveMediafile($model, $saveAsPng = TRUE)
    {
        if(! $model->newFile)
        {
            return FALSE;
        }
        // First figure out the mime type. We need to do this before
        // validating the media file.

        $mediafileType = Mediafiletype::findOne(['mimetype' => $model->newFile->type]);
        if(! $mediafileType)
        {

            throw new HttpException(500, 'Unknown media file type: ' . $model->newFile->type);
        }

        $mediafile = new Mediafile();
        $mediafile->mediafiletypeid = $mediafileType->id;
        $mediafile->title = $model->newFile->baseName;

        $mediafile->file = $model->newFile;

        if(! $mediafile->validate())
        {
            return FALSE;
        }
        // Save file contents to disk.

        // Create the directory if it does not exist yet.
        $directory = preg_replace('/\/[^\/]+$/', '', $mediafile->filePath);
        if(! file_exists($directory))
        {
            $success = @mkdir($directory, 0755, true);
            if(! $success)
            {
                Yii::error("Unable to create directory $directory");
                throw new HttpException(500, "Unable to create directory for file upload.");
            }
        }

        // Convert all images to PNG if requested and save the data to disk.
        if(preg_match('/^image\//', $mediafile->file->type) && $saveAsPng)
        {
            $converter = new ImageToPngConverter();
            $converter->setPath($mediafile->file->tempName);
            $converter->setType($mediafile->file->type);
            $converter->convert();
            $fileHandle = @fopen($mediafile->filePath, 'w');
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
        // Not an image or no conversion requested. Save file as is.
        else
        {

            if(! copy($mediafile->file->tempName, $mediafile->filePath))
            {
                Yii::error("Unable to write to file " . $mediafile->getFilePath);
                throw new HttpException(500, "Unable to copy uploaded file to destination.");
            }
        }

        @chmod($mediafile->filePath, 0755);

        unset($model->newFile);
        $mediafile->save();

        $model->addMediafile($mediafile);

        return TRUE;


    }


}
