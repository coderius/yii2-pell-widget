<?php

use \coderius\pell\Pell;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model tests\models\Post */
?>

<?php $form = ActiveForm::begin([
    'options' => ['csrf' => false]
]); 
?>

<?= $form->field($model, 'message')->widget(Pell::className(), []); ?>

<div class="form-group">
<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>