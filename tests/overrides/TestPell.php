<?php

namespace tests\overrides;

use yii\helpers\Json;
use yii\web\View;
use yii\web\JsExpression;
use coderius\pell\Pell;
use coderius\pell\PellAsset;

class TestPell extends Pell{
    
    /**
     * Override registerClientScript()
     * @return void
     */
    protected function registerClientScript($js, $key = null)
    {
        parent::registerClientScript($js, $key);
    }
}