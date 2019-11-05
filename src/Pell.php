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
use yii\widgets\InputWidget;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

/**
 * Pell widget renders a pell js plugin for WYSIWYG editing.
 */ 
class Pell extends InputWidget
{
    public $wrapperOptions = [];
    
    /**
     * @var array the options for the Pell JS plugin.
     * Please refer to the Pell JS plugin Web page for possible options.
     * @see https://github.com/jaredreich/pell/blob/master/README.md
     */
    public $clientOptions = [];

    /**
     * Generated HTML wrapper tag div
     *
     * @var string
     */
    protected $wrapper;
    
    /**
     * Initializes the Pell widget.
     * This method will initialize required property values and create wrapper html tag.
     * @return void
     */
    public function init()
    {
        parent::init();

        $textarea = $this->getTextArea();
        
        if(!isset($this->wrapperOptions['tag'])){
            $this->wrapperOptions['tag'] = 'div';
        }

        if(!isset($this->wrapperOptions['id'])){
            $this->wrapperOptions['id'] = $this->getWrapperId();
        }

        if(!isset($this->wrapperOptions['class'])){
            $this->wrapperOptions['class'] = $this->field->inputOptions['class'];
            Html::addCssStyle($this->wrapperOptions, 'height: auto', false);
        }

        $tag = $this->wrapperOptions['tag'];
        ArrayHelper::remove($this->wrapperOptions, 'tag');
        
        $this->wrapper = Html::tag($tag, $textarea, $this->wrapperOptions);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        echo $this->wrapper;
        $this->registerClientScript();

        // var_dump($this->model);
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

        $textAreaId = $this->getTextAreaId();
        $wrapperId = $this->getWrapperId();

        $this->clientOptions['element'] = new JsExpression("document.getElementById('$wrapperId')");
        $this->clientOptions['onChange'] = new JsExpression(
            "html => {
                document.getElementById('$textAreaId').innerHTML = html;
            },"
        );
        $clientOptions = Json::encode($this->clientOptions);

        //Editor js instance constant name
        $editorJsVar = "pellEditor_" . $this->getId();

        //Init plugin javascript
        $js[] = "const $editorJsVar = window.pell.init($clientOptions);";

        //If isset value data
        if($this->hasDefaultValue()){
            $defVal = $this->getDefaultValue();

            // $js[] = "$editorJsVar.content.innerHTML = $defVal";
            $js[] = new JsExpression("$editorJsVar.content.innerHTML = `$defVal`");
        }
        
        $view->registerJs(implode("\n", $js), View::POS_END);
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
        if (isset($this->options['value'])) {
            $value = $this->options['value'];
            unset($this->options['value']);
        } else {
            $value = Html::getAttributeValue($this->model, $this->attribute);
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
