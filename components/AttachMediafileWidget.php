<?php

/*****************************************************************************
 *                                                                           *
 * Widget for attaching and detaching mediafiles to a model that contains    *
 * the ModelWithMediafileBehavior.                                           *
 *                                                                           *
 * Usage: echo the output of a call to AttachMediafileWidget(parameters)     *
 *                                                                           *
 * Parameters:                                                               *
 *                                                                           *
 * model : the model to which the media files are assigned (required)        *
 *                                                                           *
 * unassignAction : the controller action that needs to be called when the   *
 * unassign button is clicked. Defaults to 'unassign'. The controller action *
 * gets two parameters: id (the id of the model) and mediafile (the id of    *
 * the media file.                                                           *
 *                                                                           *
 * form : the form object this widget is a part of (required)                *
 *                                                                           *
 * maxFileCount: the max number of media files to attach (0 = unlimited,     *
 * which  is the default).                                                   *
 *                                                                           *
 * label : the label for the entire upload widget (defaults to "Files")      *
 *                                                                           *
 * newFileLabel: the label for the file upload input (defaults to            *
 * "New files")                                                              *
 *                                                                           *
 ****************************************************************************/

namespace icalab\mediafile\components;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use kartik\file\FileInput;

class AttachMediafileWidget extends Widget
{
    public $model;

    public $unassignAction = 'unassign';

    public $form;

    public $maxFileCount = 0;

    public $label = null;

    public $newFileLabel = null;

    public function init()
    {
        parent::init();
    }

    public function run()
    {

        if($this->label === null)
        {
            $this->label = Yii::t('mediafile', 'Files');
        }
        if($this->newFileLabel === null)
        {
            $this->newFileLabel = Yii::t('mediafile', 'New file');
        }
        $html = '';

        $html .= '<div class="form-group file-preview-wrapper ">';

        $html .= '<label class="control-label col col-sm-3">'
            . $this->label
            . '</label>';
        $html .= '<div class="col col-sm-6">';

        foreach($this->model->mediafiles as $mediafile)
        {
            // Steal classes from kartik's plugin.

            if(preg_match('/^image\//', $mediafile->mediafiletype->mimetype))
            {
                $html .= '<div class="file-preview-frame">';
            }
            // Not an image.
            else
            {
                $html .= '<div class="file-preview-frame" style="width: 160px; height: auto; ">';
            }
            if(preg_match('/^image\//', $mediafile->mediafiletype->mimetype))
            {
                $html .= '<img src="'
                    . Url::to(['mediafile/view', 'id' => $mediafile->primaryKey])
                    . '" style="width: auto; height: 160px;" />';
            }
            else
            {
                //$html .= '<div class="file-preview-other"><i class="glyphicon glyphicon-file"></i></div>';
                $name = $mediafile->title;
                if(! $name )
                {
                    $name = Yii::t('mediafile', 'File') . ' ' . $mediafile->primaryKey;

                }
                $name = preg_replace('/\.[^\.]+$/', '', $name);
                $html .= '<div class="file-caption-name">';
                $html .= $name;
                $html .= '</div>';
                $html .= '<div class="file-caption-name">';
                $html .= $mediafile->mediafiletype->extension
                    . Yii::t('mediafile', '-file');
                $html .= '</div>';
            }
            $html .= '<br />';
            $html .= '<br />';
            $html .= '<a href="'
                . Url::to([$this->unassignAction, 'id' => $this->model->primaryKey, 'mediafile' => $mediafile->primaryKey])
                . '" class="btn btn-warning center-block">'
                . Yii::t('mediafile', 'Unassign')
                . '</a>';
            $html .= '<br />';
            $html .= '</div>'; // preview pane
        }

        $html .= '</div>'; // col
        $html .= '</div>'; // form-group

        $html .= '<div class="file-upload-wrapper">';
        // HACK: if you do not manually append [] to the name of the
        // attribute, only one file will be uploaded.
        $html .= $this->form->field($this->model, 'newFiles[]')->widget(FileInput::classname(), [
            'options' => ['accept' => ['image/*', 'video/*'], 'multiple' => true],
            'pluginOptions' => [
                'showUpload' => false,
                'maxFileCount' => $this->maxFileCount,
                'autoReplace' => true,
                ],
        ])
        ->label($this->newFileLabel);
        $html .= '</div>';

        return $html;
    }
}
