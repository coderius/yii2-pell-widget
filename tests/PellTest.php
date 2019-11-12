<?php
/**
 * @copyright Copyright (c) 2013-2019 Sergio Coderius!
 * @link https://coderius.biz.ua
 * @license This program is free software: the MIT License (MIT)
 * @author Sergio Coderius <sunrise4fun@gmail.com>
 */

namespace tests;

use coderius\pell\Pell;
use tests\models\Post;
use yii\web\View;
use Yii;

class PellTest extends TestCase
{

    public function testRenderWithModel()
    {
        $model = new Post();
        $out = Pell::widget([
            'model'  => $model,
            'attribute' => 'message',
        ]);
        $expected = '<div id="pell-wrap-w0" class="form-control" style="height: auto"><textarea id="post-message" name="Post[message]" style="display:none;"></textarea></div>';
        $this->assertEqualsWithoutLE($expected."\n", $out);
    }

    public function testRenderWithNameAndValue()
    {
        $out = Pell::widget([
            'value'  => 'Some',
            'name' => 'input-name',
        ]);
        $expected = '<div id="pell-wrap-w1" class="form-control" style="height: auto"><textarea id="pell-textarea-w1" name="input-name" style="display:none;">Some</textarea></div>';
        $this->assertEqualsWithoutLE($expected."\n", $out);
    }

    public function testRenderWithActiveForm()
    {
        $model = new Post();
        $view = Yii::$app->getView();
        $content = $view->render('//form-with-widget', ['model' => $model]);
        $actual = $view->render('//layouts/main', ['content' => $content]);
        $expected = file_get_contents(__DIR__ . '/data/test-form-wich-widget.bin');
        $this->assertEqualsWithoutLE($expected, $actual);
    }
}    