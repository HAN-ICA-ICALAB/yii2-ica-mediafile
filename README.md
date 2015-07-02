# yii2-ica-mediafile
Extension for attaching media files to models

## Installation

1. Install the mediafile extension using composer:
```
composer require icalab/yii2-ica-mediafile
```
2. Run the provided migration:
```
php yii migrate --migrationPath=@icalab/mediafile/migrations
```
3. Create a directory mediafiles under the web directory and make it writable
   for the web server.

## Usage

The relevant code files contain documentation at the top of the files. There
is also the Voorbeeld model and the VoorbeeldController that you can use to
see an example of how this module works.

In short:

1. Create your model.
2. Attach the ModelWithMediafileBehavior behavior to your model.
3. Create a join table named yourmodel_mediafile containing a column parentid
   and a column mediafileid.
4. Attach the behavior ControllerWithMediafileBehavior to your controller.
   Supply the class name of your model as a parameter (modelClass).
5. Create an action actionUnassign($id, $mediafile) in your controller and
   make it call the unassignMediafile($id, $mediafile) method that comes from
   the ControllerWithMediafileBehavior behavior.
6. In your update method, handle uploading of files by adding code like the
   following:
```php
            $model->newFile = UploadedFile::getInstance($model, 'newFile');
            if($model->validate() && (! $model->newFile || $this->saveMediaFile($model)))
            {
                $model->save();
                $this->redirect(['update', 'id' => $id]);
            }
```
7. In your form view, show the output of the AttachMediafileWidget widget.
   Supply the model and the form as a parameter to this widget:

```php
echo AttachMediafileWidget::widget(['model' => $model, 'form' => $form]);
```

## Notes

* Uploaded media files can be accessed from the web using the url
  mediafile/view?id=id_of_file
* By default uploaded images are converted to PNG to avoid further loss of
  quality. This was a requirement for the project we built this module for.
  If you don't want this, pass an extra parameter FALSE to the saveMediafile
  method the model inherits from the ModelWithMediafileBehavior behavior.



