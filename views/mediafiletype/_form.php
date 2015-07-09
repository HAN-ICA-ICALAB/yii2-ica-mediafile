<?php
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model icalab\mediafile\models\Mediafiletype */

use icalab\mediafile\MediafileModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php
$form = ActiveForm::begin([
    'id' => 'mediafiletype-form',
    'layout' => 'horizontal'
]);
echo $form->errorSummary($model);


echo $form->field($model, 'name');
echo $form->field($model, 'mimetype');
echo $form->field($model, 'extension');

if($model->isNewRecord)
{
    echo Html::submitButton(Yii::t('mediafile', 'Create'), ['class' => 'btn btn-primary']);
}
else
{
    echo Html::submitButton(Yii::t('mediafile', 'Update'), ['class' => 'btn btn-primary']);
}

ActiveForm::end();

