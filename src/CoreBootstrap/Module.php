<?php

namespace CoreBootstrap;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements
    ConfigProviderInterface,
    ViewHelperProviderInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'alert'   => 'CoreBootstrap\View\Helper\Alert',
                'formRow' => 'CoreBootstrap\Form\View\Helper\FormRow',
            ),
        );
    }
}
