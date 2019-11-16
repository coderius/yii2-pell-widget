<?php

namespace tests;

use Yii;
use yii\base\Action;
use yii\base\Module;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\View;
use yii\web\AssetManager;
/**
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockWebApplication();
    }
    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->destroyApplication();
    }
    /**
     * @param array $config
     * @param string $appClass
     */
    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfds@68F+-dDf89oq',
                    'hostInfo' => 'http://domain.com',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => 'index.php',
                    'url' => '/tests',//needed for `$view->render`
                ],
                'user' => [
                    'class' => 'yii\web\User',
                ],
                'urlManager' => [
                    'class' => 'yii\web\UrlManager',
                    'enablePrettyUrl' => true,
                    'showScriptName' => false,
                    'enableStrictParsing' => true,
                    'rules' => [],
                ],
                'assetManager' => [
                    'basePath' => '@tests/assets',
                    'baseUrl' => '/',
                    'appendTimestamp' => true,
                ], 
            ],
            'modules' => [],
            'container' => [
                'definitions' => [],
            ], 
               
        ], $config));
    }
    
    /**
     * Mocks controller action with parameters
     *
     * @param string $controllerId
     * @param string $actionID
     * @param string $moduleID
     * @param array $params
     */
    protected function mockAction($controllerId, $actionID, $moduleID = null, $params = [])
    {
        Yii::$app->controller = $controller = new Controller($controllerId, Yii::$app);
        $controller->actionParams = $params;
        $controller->action = new Action($actionID, $controller);
        if ($moduleID !== null) {
            $controller->module = new Module($moduleID);
        }
    }
    /**
     * Removes controller
     */
    protected function removeMockedAction()
    {
        Yii::$app->controller = null;
    }
    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
        Yii::$container = new Container();
    }
    /**
     * Asserting two strings equality ignoring line endings
     *
     * @param string $expected
     * @param string $actual
     */
    public function assertEqualsWithoutLE($expected, $actual)
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Creates a view for testing purposes
     *
     * @return View
     */
    protected function getView()
    {
        $view = new View();
        $view->setAssetManager(
            new AssetManager(
                [
                    'basePath' => '@tests/assets',
                    'baseUrl' => '/',
                ]
            )
        );
        return $view;
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }
}