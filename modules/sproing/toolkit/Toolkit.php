<?php

namespace modules\sproing\toolkit;

use modules\sproing\toolkit\twigextensions\ToolkitTwigExtension;
use modules\sproing\toolkit\fields\EncryptedText as EncryptedTextField;
use modules\sproing\toolkit\services\TeamworkService;
use modules\sproing\toolkit\variables\ToolkitVariable;
use modules\sproing\toolkit\web\assets\CustomAssets;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use craft\events\TemplateEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;
use yii\base\Module;

class Toolkit extends Module
{
    public static $instance;

    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@sproing/toolkit', $this->getBasePath());
        $this->controllerNamespace = 'modules\sproing\toolkit\controllers';

        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    public function init()
    {
        parent::init();
        self::$instance = $this;

        Craft::$app->view->twig->addExtension(new ToolkitTwigExtension());

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = EncryptedTextField::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('toolkit', ToolkitVariable::class);
            }
        );

        // Control panel styles
        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {

                    // Get view
                    $view = Craft::$app->getView();

                    // Load CSS file
                    $view->registerAssetBundle(CustomAssets::class);
                }
            );
        }
    }
}
