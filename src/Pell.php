<?php
/**
 * @copyright Copyright (c) 2013-2019 Sergio Coderius!
 * @link https://coderius.biz.ua
 * @license This program is free software: the MIT License (MIT)
 * @author Sergio Coderius <sunrise4fun@gmail.com>
 */

namespace coderius\pell;

use yii\helpers\Html;
use yii\helpers\Json;
use coderius\pell\PellInputWidget;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\base\InvalidParamException;

/**
 * Pell widget renders a pell js plugin for WYSIWYG editing.
 * 
 * For example to use the Pell widget with a [[\yii\base\Model|model]]:
 *
 * ```php
 * echo Pell::widget([
 *     'model' => $model,
 *     'attribute' => 'text',
 * ]);
 * ```
 *
 * The following example will used not as an element of form:
 *
 * ```php
 * echo Pell::widget([
 *     'asFormPart'  => false,
 *     'value'  => $value,
 * ]);
 * ```
 * 
 * You can also use this widget in an [[\yii\widgets\ActiveForm|ActiveForm]] using the [[\yii\widgets\ActiveField::widget()|widget()]]
 * method, for example like this:
 * 
 * ```php
 * use coderius\pell\Pell;
 * 
 * <?= $form->field($model, 'text')->widget(Pell::className(), []);?>
 * ```
 * 
 * Note that if Pell widget using inside form - [Pell::asFormPart] must be set to true (by default)
 * 
 * @author Sergio Coderius <sunrise4fun@gmail.com>
 */ 
class Pell extends PellInputWidget
{
    /**
     * If widget used inside form
     *
     * @var boolean
     */
    public $asFormPart = true;

    /**
     * Container tag for widget
     *
     * @var array
     */
    public $wrapperOptions = [];
    
    /**
     * @var array the options for the Pell JS plugin.
     * Please refer to the Pell JS plugin Web page for possible options.
     * @see https://github.com/jaredreich/pell/blob/master/README.md
     */
    public $clientOptions = [];

    /**
     * Initializes the Pell widget.
     * This method will initialize required property values and create wrapper html tag.
     * @return void
     */
    public function init()
    {
        parent::init();

        //If widget is used as part of the form, setting [clientOptions['onChange']] from widget options not allowed
        if(isset($this->clientOptions['onChange']) && $this->asFormPart){
            throw new InvalidParamException("Param 'onChange' cannot be specified if the widget is used as part of the form");
        }

        if(!isset($this->wrapperOptions['tag'])){
            $this->wrapperOptions['tag'] = 'div';
        }

        if(!isset($this->wrapperOptions['id'])){
            $this->wrapperOptions['id'] = $this->getWrapperId();
        }

        if(!isset($this->wrapperOptions['class'])){
            //From yii\widgets\ActiveField [$inputOptions = ['class' => 'form-control']]
            $this->wrapperOptions['class'] = 'form-control';

            Html::addCssStyle($this->wrapperOptions, 'height: auto', false);
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        echo $this->renderWidget() . "\n";

        $this->registerClientScript();
    }

    /**
     * Renders the Pell widget.
     * @return string the rendering result.
     */
    protected function renderWidget()
    {
        $content = null;

        //If [$this->asFormPart === true] then create hidden textarea inside wrapper to seva passed data
        if($this->asFormPart){
            $content = $this->getTextArea();
        }
        
        $tag = $this->wrapperOptions['tag'];
        ArrayHelper::remove($this->wrapperOptions, 'tag');
        
        return Html::tag($tag, $content, $this->wrapperOptions);
    }    

    /**
     * Registers Pell js plugin
     * @return void
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();
        PellAsset::register($view);
        $wrapperId = $this->getWrapperId();

        //Nrrd set element as first in client cinfig array (bug in plugin!)
        $element = new JsExpression("document.getElementById('$wrapperId')");
        $this->clientOptions = array_merge(['element' => $element], $this->clientOptions);

        //If widget used inside form and needed generete [Html::textarea] or [Html::activeTextarea]
        if($this->asFormPart){
            $textAreaId = $this->getTextAreaId();
            //Write content from editor to hidden texarea when that was changed
            $this->clientOptions['onChange'] = new JsExpression(
                "html => {
                    document.getElementById('$textAreaId').innerHTML = html;
                },"
            );
        }
        
        $clientOptions = Json::encode($this->clientOptions);

        //Editor js instance constant name
        $editorJsVar = "pellEditor_" . $this->getId();

        //Init plugin javascript
        $js[] = "const $editorJsVar = pell.init($clientOptions);";
        $js[] = "console.log($clientOptions);";

        //If isset default value like value from db, or if set [$this->value]
        if($this->hasDefaultValue()){
            $defVal = $this->getDefaultValue();

            //Pass value to editor
            $js[] = new JsExpression("$editorJsVar.content.innerHTML = `$defVal`");
        }
        
        $view->registerJs(implode("\n", $js), View::POS_END);
    }

    /**
     * Generates a textarea tag for the given model attribute.
     * The model attribute value will be used as the content in the textarea.
     *
     * @return void
     */
    protected function getTextArea()
    {
        Html::addCssStyle($this->options, 'display:none;');

        if ($this->hasModel()) {
            $textarea = Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            $textarea = Html::textarea($this->name, $this->value, $this->options);
        }

        return $textarea;
    }

    /**
     * Return wrapper id for wrapper tag id attribute
     *
     * @return string
     */
    protected function getWrapperId()
    {
        return 'pell-'. $this->getId();
    }

    /**
     * Return a textarea attribute id
     *
     * @return string
     */
    protected function getTextAreaId()
    {
        return $this->options['id'];
    }

    /**
     * Return default value returned of model attribute if exists or by passed to `options['value']`
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }

        return $value;
    }

    /**
     * Check is default value is not empty
     *
     * @return boolean
     */
    public function hasDefaultValue()
    {
        return !empty($this->getDefaultValue());
    }
}
