<?php

namespace CoreBootstrapTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use CoreBootstrap\View\Helper\Alert;

class AlertTest extends TestCase
{
    public function assertPreConditions()
    {
        $helper = new Alert();
        $this->assertInstanceOf('CoreBootstrap\View\Helper\Alert', $helper);
    }

    public function testAlertWithOnlyMessage()
    {
        $helper = new Alert();

        $result = $helper('Message');

        $this->assertStringStartsWith('<div class="alert">', $result);
        $this->assertNotContains('<h4>', $result);
        $this->assertContains(
            '<button type="button" class="close" data-dismiss="alert">&times;</button>',
            $result
        );
        $this->assertContains('<p>Message</p>', $result);
        $this->assertStringEndsWith('</div>', $result);
    }

    public function testAlertWithTitle()
    {
        $helper = new Alert();

        $result = $helper('Message', 'Title');
        $this->assertContains('<h4>Title</h4>', $result);
    }

    public function testAlertWithConnotation()
    {
        $helper = new Alert();

        $result = $helper('Message', null, 'success');
        $this->assertStringStartsWith('<div class="alert alert-success">', $result);
    }

    public function testNotDismissableAlert()
    {
        $helper = new Alert();

        $result = $helper('Message', null, null, false);
        $this->assertNotContains(
            '<button type="button" class="close" data-dismiss="alert">&times;</button>',
            $result
        );
    }
}
