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
 * Pell renders a pell js plugin for WYSIWYG editing.
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
     * Generated HTML tag textarea
     *
     * @var string
     */
    protected $textarea;

    /**
     * Generated HTML wrapper tag div
     *
     * @var string
     */
    protected $wrapper;
    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->textarea = $this->getTextArea();
        
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
        
        $this->wrapper = Html::tag($tag, $this->textarea, $this->wrapperOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo $this->wrapper;
        $this->registerClientScript();

        // var_dump($this->model);
    }

    /**
     * Registers Pell js plugin
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
     * Undocumented function
     *
     * @return void
     */
    protected function getWrapperId()
    {
        return 'pell-'. $this->getId();
    }

    /**
     * Undocumented function
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
     * Undocumented function
     *
     * @return void
     */
    protected function getTextAreaId()
    {
        return $this->options['id'];
    }

    /**
     * Undocumented function
     *
     * @return void
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
     * Undocumented function
     *
     * @return boolean
     */
    public function hasDefaultValue()
    {
        return !empty($this->getDefaultValue());
    }
}
