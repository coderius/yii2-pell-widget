<?php

use \coderius\pell\Pell;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php
    //--------------------------------------------------------------------
    //Without form in single div element. Result passed to editor hendler 
    //by `'onChange'` client event function
    //--------------------------------------------------------------------
    echo \coderius\pell\Pell::widget([
        'asFormPart'  => false,
        'value'  => 'Some',
        'clientOptions' =>[
            'onChange' => new JsExpression(
                "html => {
                    console.log(html);
                }"
            )
        ]
    ]);
?>


<?php 
//------------------------------------------------------------------------
//Usage in Active Form widget
//------------------------------------------------------------------------
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'text')->widget(\coderius\pell\Pell::className(), []); ?>

<div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>


<?php 
//------------------------------------------------------------------------
//Usage in Html::Form without model
//------------------------------------------------------------------------
?>
<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>

<?php
//Used `Html::textarea($this->name, $this->value, $this->inputOptions);`
//to create textarea with hidden css style
    echo \coderius\pell\Pell::widget([
        'value'  => 'Some',
        'name' => 'input-name',
        'inputOptions' =>[
            'id' => 'textareaId',
        ],
        'clientOptions' =>[
            
        ]
    ]);

    echo \coderius\pell\Pell::widget([
        'value'  => 'Some',
        'name' => 'input-name',
    ]);

?>

<?= Html::submitButton('Submit', ['class' => 'submit']) ?>

<?php 
//------------------------------------------------------------------------
//Usage in Html::Form with model
//------------------------------------------------------------------------
?>
<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>

<?php
//Used `Html::activeTextarea($this->model, $this->attribute, $this->inputOptions);`
//to create textarea with hidden css style
    echo \coderius\pell\Pell::widget([
        'model'  => $model,
        'attribute' => 'text',
        'inputOptions' =>[
            'id' => 'textareaId',
        ],
        'clientOptions' =>[
            
        ]
    ]);

    echo \coderius\pell\Pell::widget([
        'model'  => $model,
        'attribute' => 'text',
    ]);
?>

<?= Html::submitButton('Submit', ['class' => 'submit']) ?>