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
 ****************************************************************************/

namespace icalab\mediafile\components;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class AttachMediafileWidget extends Widget
{
    public $model;

    public $unassignAction = 'unassign';

    public $form;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $html = '<div class="row"><div class="form-group">';

        $html .= '<label class="control-label col-sm-3">'
            . Yii::t('mediafile', 'Files')
            . '</label>';
        $html .= '<div class="col-sm-9">';
        $html .= '<div class="container-fluid">';

        $html .= '<div class="row ">';
        foreach($this->model->mediafiles as $mediafile)
        {
            $html .= '<div class="col-sm-3">';
            if(preg_match('/^image\//', $mediafile->mediafiletype->mimetype))
            {
            $html .= '<img src="'
                . Url::to(['mediafile/view', 'id' => $mediafile->primaryKey])
                . '" class="img-thumbnail" />';
            }
            else
            {
                $name = $mediafile->title;
                if(! $name )
                {
                    $name = Yii::t('mediafile', 'File') . ' ' . $mediafile->primaryKey;

                }
                $html .= $name;
            }
            $html .= '<a href="'
                . Url::to([$this->unassignAction, 'id' => $this->model->primaryKey, 'mediafile' => $mediafile->primaryKey])
                . '" class="btn btn-warning center-block">'
                . Yii::t('mediafile', 'Unassign')
                . '</a>';
            $html .= '<p></p>';
            $html .= '</div>';
        }
        $html .= '</div>'; // row (files)

        $html .= '</div>'; // container
        $html .= '</div>'; // col
        $html .= '</div></div>'; // form-group, row


        $html .= '<div class="row">';

        $html .= $this->form->field($this->model, 'newFile')->fileInput()->label(Yii::t('mediafile', 'New file'));
        
        $html .= '</div>';
        return $html;
    }
}
