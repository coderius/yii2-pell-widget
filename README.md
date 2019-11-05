# Pell content editor Widget for Yii2

Renders a [Pell WYSIWYG text editor plugin](https://github.com/jaredreich/pell) widget.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require coderius/yii2-pell-widget:"@dev"
```
or add

```json
"coderius/yii2-pell-widget" : "@dev"
```

to the require section of your application's `composer.json` file.

## Usage


```

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
