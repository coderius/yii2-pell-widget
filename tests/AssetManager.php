<?php
/**
 * @copyright Copyright (c) 2013-2019 Sergio Coderius!
 * @link https://coderius.biz.ua
 * @license This program is free software: the MIT License (MIT)
 * @author Sergio Coderius <sunrise4fun@gmail.com>
 */

namespace tests;
/**
 * AssetManager
 */
class AssetManager extends \yii\web\AssetManager
{
    private $_hashes = [];
    private $_counter = 0;
    /**
     * @inheritdoc
     */
    public function hash($path) {
        if (!isset($this->_hashes[$path])) {
            $this->_hashes[$path] = $this->_counter++;
        }
        return $this->_hashes[$path];
    }
}