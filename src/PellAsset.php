<?php
/**
 * @copyright Copyright (c) 2013-2019 Sergio Coderius!
 * @link https://coderius.biz.ua
 * @license This program is free software: the MIT License (MIT)
 * @author Sergio Coderius <sunrise4fun@gmail.com>
 */
namespace coderius\pell;

use yii\web\AssetBundle;

class PellAsset extends AssetBundle
{
    public $sourcePath = '@npm/pell/dist';

    public $css = [
        YII_DEBUG ? 'pell.css' : 'pell.min.css'
    ];

    public $js = [
        YII_DEBUG ? 'pell.js' : 'pell.min.js'
    ];

}
