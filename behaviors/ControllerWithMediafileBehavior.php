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
     * Save a new media file and attach it to a specific model. The newFiles
     * attribute of the model must be set tp an instance of UploadedFile.
     * 
     * Example:
     * $model = new SomeModel();
     * $model->newFiles = UploadedFile::getInstances($model, 'newFiles');
     * if($model->newFiles)
     * {
     *   $this->saveMediafiles($model);
     * }
     *
     * @param model the model to attach the file(s) to. The newFiles attribute of 
     * the model must be set, otherwise nothing happens
     * @param saveAsPng if true, images will be saved as png (this is the 
     * default)
     * @return whether saving succeeded or not
     */
    public function saveMediafiles($model, $saveAsPng = TRUE)
    {
        if(! $model->newFiles)
        {
            return FALSE;
        }

        // We need this in the converter below.
        $pngType = Mediafiletype::findOne(['mimetype' => 'image/png']);

        $mediafiles = [];

        // First validate the new files. If the new files are not valid,
        // abort.
        foreach($model->newFiles as $newFile)
        {
            $mediafileType = Mediafiletype::findOne(['mimetype' => $newFile->type]);
            // Mime type detection does not always work flawlessly. If no mime 
            // type is supplied, check if we recognize the file extension.
            if(! $mediafileType)
            {
                $mediafileType = Mediafiletype::findOne(['extension' => $newFile->extension]);
            }
            if(! $mediafileType)
            {
                throw new HttpException(500, 'Unknown media file type: ' . $newFile->type);
            }

            $mediafile = new Mediafile();
            $mediafile->mediafiletypeid = $mediafileType->id;
            $mediafile->title = $newFile;

            $mediafile->file = $newFile;

            if(! $mediafile->validate())
            {
                return FALSE;
            }
            $mediafiles[] = $mediafile;
        }

        // If we're still here, all media files were ok. Save them.
        foreach($mediafiles as $mediafile)
        {

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
                $mediafile->mediafiletypeid = $pngType->primaryKey;
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

            $mediafile->save();

            $model->addMediafile($mediafile);
        }

        unset($model->newFiles);

        return TRUE;


    }


}
