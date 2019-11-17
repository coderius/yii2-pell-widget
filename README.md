Pell content WYSIWYG editor Widget for Yii2
===========================================

[![Software License](https://img.shields.io/github/license/coderius/yii2-pell-widget)](LICENSE.md)
[![Code Coverage](https://scrutinizer-ci.com/g/coderius/yii2-pell-widget/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/coderius/yii2-pell-widget/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/coderius/yii2-pell-widget/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/coderius/yii2-pell-widget.svg?style=flat-square)](https://scrutinizer-ci.com/g/coderius/yii2-pell-widget/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/coderius/yii2-pell-widget/badges/build.png?b=master)](https://scrutinizer-ci.com/g/coderius/yii2-pell-widget/build-status/master)

Renders a [Pell WYSIWYG text editor plugin](https://github.com/jaredreich/pell) widget.

![Live demo](https://raw.githubusercontent.com/jaredreich/pell/master/demo.gif?raw=true "Demo")

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require coderius/yii2-pell-widget:"~1.0"
```
or add

```json
"coderius/yii2-pell-widget" : "~1.0"
```

to the require section of your application's `composer.json` file.

## Usage

For example to use the pell editor with a [[\yii\base\Model|model]]:

 ```php
echo Pell::widget([
    'model' => $model,
    'attribute' => 'text',
]);
  ```
 
Inside form without model:

 ```php
$value = 'textarea some content';

echo \coderius\pell\Pell::widget([
    'name' => 'textarea-name',
    'value'  => $value,
    'clientOptions' =>[]
]);
```

The following example will used not as an element of form:
 
  ```php
echo Pell::widget([
    'asFormPart'  => false,
    'value'  => $value,
    'clientOptions' =>[
        'onChange' => new JsExpression(
            "html => {
                console.log(html);
            },"
        )
    ]
]);
  ```
  
You can also use this widget in an [[\yii\widgets\ActiveForm|ActiveForm]] using the [[\yii\widgets\ActiveField::widget()|widget()]] method, for example like this:

```php

use coderius\pell\Pell;

<?= $form->field($model, 'text')->widget(Pell::className(), []);?>
```

### About ClientOptions 

Please, remember that if you are required to add javascript to the configuration of the js plugin and is required to be 
plain JS, make use of `JsExpression`. That class was made by Yii for that specific purpose. For example:
 
```php 
// Having the following scenario
<script> 
    function jsFunctionToBeCalled() {
        // ...
    }
</script>

<?= $form->field($model, 'content')->widget(Pell::className(), [
        'clientOptions' => [
            'defaultParagraphSeparato' => 'div',

            // ...

            'actions' => [
                'bold',
                'italic',
                'underline',
                'strikethrough',
                'heading1',
                'heading2',
                'paragraph',
                'quote',
                'olist',
                'ulist',
                'code',
                'line',
                'link',
                'image',
                [
                    'name'   => 'backColor',
                    'icon'   => '<div style="background-color:pink;">A</div>',
                    'title'  => 'Highlight Color',
                    // this will render the function name without quotes on the configuration options of the plugin
                    'result' => new JsExpression('jsFunctionToBeCalled')
                ],
            ],
            
            // ...
        ]
        
    ]
]); ?>
```

## Examples widget usage
Please see [Examples usage in yii2 view files](https://github.com/coderius/yii2-pell-widget/blob/master/examples/some-yii2-view.php) for more examples.

## Testing

``` bash
$ phpunit
```

## Further Information

Please, check the [Pell plugin github](https://github.com/jaredreich/pell) documentation for further 
information about its configuration options.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Sergio Coderius](https://github.com/coderius)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
 
<i>Web development has never been so fun!</i>  
[coderius.biz.ua](https://coderius.biz.ua)
