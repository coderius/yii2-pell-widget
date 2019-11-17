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
use tests\overrides\TestPell;
use yii\base\InvalidArgumentException;
use yii\web\JsExpression;
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

    public function testBuildClientScriptMethod()
    {
        $key = 'test-pell-js';
        $model = new Post();
        $widget = TestPell::begin(
            [
                'model'  => $model,
                'attribute' => 'message',
            ]
        );
        $view = $this->getView();
        $widget->setView($view);
        $widget->setId('w10');
        
        $class = new \ReflectionClass('tests\\overrides\\TestPell');
        $js = $class->getMethod('buildClientScript');
        $js->setAccessible(true);
        $js = $js->invoke($widget);
        
        $test = <<<JS

const pellEditor_w10 = pell.init({"element":document.getElementById('pell-wrap-w10'),"onChange":html => {
    document.getElementById('post-message').innerHTML = html;
},});

JS;
        $this->assertEquals($test, $js);
    }

    public function testRegisterClientScriptMethod()
    {
        $key = 'test-pell-js';
        $model = new Post();
        $widget = TestPell::begin(
            [
                'model'  => $model,
                'attribute' => 'message',
            ]
        );
        $view = $this->getView();
        $widget->setView($view);
        $widget->setId('w10');
        
        $class = new \ReflectionClass('tests\\overrides\\TestPell');
        $js = $class->getMethod('buildClientScript');
        $js->setAccessible(true);
        $js = $js->invoke($widget);
        
        $method = $class->getMethod('registerClientScript');
        $method->setAccessible(true);
        $method = $method->invokeArgs($widget, [$js, $key]);
        
        $test = <<<JS

const pellEditor_w10 = pell.init({"element":document.getElementById('pell-wrap-w10'),"onChange":html => {
    document.getElementById('post-message').innerHTML = html;
},});

JS;
        $this->assertEquals($test, $view->js[View::POS_END][$key]);
    }    

    public function testInitTextareaWithoutName()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $widget = Pell::widget(
            [
                'value' => 'message',
            ]
        );
    }  
    
    public function testInitTextareaWithOnChange()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $widget = Pell::widget(
            [
                'name' => 'attr_name',
                'value' => 'message',
                'clientOptions' =>[
                    'onChange' => new JsExpression(
                        "html => {
                            console.log(html);
                        },"
                    )
                ]
            ]
        );
    } 

    public function testInitDefaultParams()
    {
        $widget = TestPell::begin(
            [
                'id' => 'w11',
                'name' => 'attr_name',
                'value' => 'message',
            ]
        );
        $view = $this->getView();
        $widget->setView($view);
        $this->assertEquals('w11', $widget->getId());
        $this->assertEquals('pell-textarea-w11', $widget->getTextAreaId());
        $this->assertEquals('div', $widget->getWrapperTag());
        $this->assertEquals('pell-wrap-w11', $widget->getWrapperId());
        $this->assertEquals('form-control', $widget->getWrapperClass());
    } 

    public function testCantSetClientOptionsElement()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $widget = TestPell::begin(
            [
                'name' => 'attr_name',
                'value' => 'message',
                'clientOptions' =>[
                    'element' => new JsExpression(
                        "document.getElementById('someId')"
                    )
                ]
            ]
        );
       
    }

}    