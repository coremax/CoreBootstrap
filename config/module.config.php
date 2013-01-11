<?php

return array(
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../asset',
            ),
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'formRow' => 'CoreBootstrap\Form\View\Helper\FormRow',
            'alert'   => 'CoreBootstrap\View\Helper\Alert',
        ),
    ),
);
