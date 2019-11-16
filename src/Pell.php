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
     * If widget used inside form, then param `$asFormPart` must be set as true and tag textarea (hidden) must be created
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

    protected $clientPluginInstanceName = 'pell';

    /**
     * Initializes the Pell widget.
     * This method will initialize required property values and create wrapper html tag.
     * @return void
     */
    public function init()
    {
        parent::init();

        //Attribute id in textarea must be set
        if(!isset($this->inputOptions['id']) && true === $this->asFormPart){
            $pell = $this->clientPluginInstanceName;
            $this->inputOptions['id'] = "$pell-textarea-" . $this->getId();
        }

        //In Html::textarea attribute name must be set
        if(!$this->name && true === $this->asFormPart && false === $this->hasModel()){
            throw new InvalidParamException("Param 'name' must be specified for textarea attribute name.");
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

        //If widget is used as part of the form, setting [clientOptions['onChange']] from widget options not allowed
        if(isset($this->clientOptions['onChange']) && $this->asFormPart){
            throw new InvalidParamException("Param 'onChange' cannot be specified if the widget is used as part of the form.");
        }

        if(isset($this->clientOptions['element'])){
            throw new InvalidParamException("Param 'element' cannot be specified. This param set by widget.");
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        echo $this->renderWidget() . "\n";
        $js = $this->buildClientScript();
        $this->registerClientScript($js);
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
        
        $tag = $this->getWrapperTag();
        ArrayHelper::remove($this->wrapperOptions, 'tag');
        
        return Html::tag($tag, $content, $this->wrapperOptions);
    }    

    /**
     * Build js script
     *
     * @return string
     */
    protected function buildClientScript()
    {
        $js = [];
        $wrapperId = $this->getWrapperId();
        $element = new JsExpression("document.getElementById('$wrapperId')");

        $this->clientOptions['element'] = $element;
        
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
        $pell = $this->clientPluginInstanceName;

        //Editor js instance constant name
        $editorJsVar = "{$pell}Editor_" . $this->getId();

        //Init plugin javascript
        $js[] = "";//set \n
        $js[] = "const $editorJsVar = $pell.init($clientOptions);";
        
        //If isset default value like value from db, or if set `$this->value`
        if($this->hasDefaultValue()){
            $defVal = $this->getDefaultValue();

            //Pass value to editor
            $js[] = new JsExpression("$editorJsVar.content.innerHTML = `$defVal`");
        }
        $js[] = "";//set \n

        return implode("\n", $js);
    }    

    /**
     * Registers Pell js plugin
     * @param string $js the JS code block to be registered
     * @param string $key the key that identifies the JS code block. If null, it will use
     * $js as the key. If two JS code blocks are registered with the same key, the latter
     * will overwrite the former.
     * 
     * @return void
     */
    protected function registerClientScript($js, $key = null)
    {
        $view = $this->getView();
        PellAsset::register($view);
        $view->registerJs($js, View::POS_END, $key);
    }

    /**
     * Generates a textarea tag for the given model attribute.
     * The model attribute value will be used as the content in the textarea.
     *
     * @return void
     */
    protected function getTextArea()
    {
        Html::addCssStyle($this->inputOptions, 'display:none;');

        if ($this->hasModel()) {
            $textarea = Html::activeTextarea($this->model, $this->attribute, $this->inputOptions);
        } else {
            $textarea = Html::textarea($this->name, $this->value, $this->inputOptions);
        }

        return $textarea;
    }

    /**
     * Return wrapper id for wrapper tag id attribute
     *
     * @return string
     */
    public function getWrapperId()
    {
        $pell = $this->clientPluginInstanceName;
        return "$pell-wrap-". $this->getId();
    }

    public function getWrapperClass()
    {
        return $this->wrapperOptions['class'];
    }

    /**
     * Return a textarea attribute id
     *
     * @return string
     */
    public function getTextAreaId()
    {
        return $this->inputOptions['id'];
    }

    /**
     * Return wrapper tag name
     *
     * @return string
     */
    public function getWrapperTag()
    {
        return $this->wrapperOptions['tag'];
    }
    
    /**
     * Return default value returned of model attribute if exists or by passed to `inputOptions['value']`
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
