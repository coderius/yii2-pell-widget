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

/**
 * Pell renders a pell js plugin for WYSIWYG editing.
 */ 
class Pell extends InputWidget
{
    /**
     * @var array the options for the Pell JS plugin.
     * Please refer to the Pell JS plugin Web page for possible options.
     * @see https://github.com/jaredreich/pell/blob/master/README.md
     */
    public $clientOptions = [];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
        $this->registerClientScript();
    }

    /**
     * Registers Pell js plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();

        PellAsset::register($view);

        $id = $this->options['id'];

        $this->clientOptions['element'] = "#$id";
        
        $options = Json::encode($this->clientOptions);

        $js[] = "pell.init($options);";
        
        $view->registerJs(implode("\n", $js));
    }
}
