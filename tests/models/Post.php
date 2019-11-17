<?php
/**
 * @copyright Copyright (c) 2013-2019 Sergio Coderius!
 * @link https://coderius.biz.ua
 * @license This program is free software: the MIT License (MIT)
 * @author Sergio Coderius <sunrise4fun@gmail.com>
 */

namespace tests\models;

use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    public $message;
    public static $db;
    
    public static function getDb()
    {
        return self::$db;
    }
}