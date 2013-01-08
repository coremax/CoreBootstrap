<?php

namespace CoreBootstrap\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Alert extends AbstractHelper
{
    /**
     * @param  string  $message
     * @param  string  $title
     * @param  string  $connotation
     * @param  boolean $dismissable
     * @return string
     */
    public function __invoke($message, $title = null, $connotation = null, $dismissable = true)
    {
        $html = '<div class="alert';

        if ($connotation) {
            $html .= ' alert-' . $connotation;
        }

        $html .= '">';

        if ($dismissable) {
            $html .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        }

        if ($title) {
            $html .= '<h4>' . $title . '</h4>';
        }

        $html .= '<p>' . $message . '</p>';

        $html .= '</div>';

        return $html;
    }
}
