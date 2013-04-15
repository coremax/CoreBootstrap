<?php

namespace CoreBootstrap\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\Form\View\Helper\FormElement;

class FormRow extends \Zend\Form\View\Helper\FormRow
{
    /**
     * @var string
     */
    protected $formStyle = 'horizontal';

    /**
     * Templates to use for a bootstrap element
     *
     * %1$s - label open
     * %2$s - label
     * %3$s - label close
     * %4$s - element
     * %5$s - errors
     * %6$s - help
     * %7$s - status
     *
     * @var array
     */
    protected $defaultElementTemplates = array(
        'vertical'   => '%1$s%2$s%3$s%4$s%5$s',
        'inline'     => '%4$s%5$s',
        'search'     => '%4$s%5$s',
        'horizontal' => '<div class="control-group %6$s">%1$s%2$s%3$s<div class="controls">%4$s%5$s</div></div>',
        'tableHead'  => '<th>%2$s</th>',
        'tableRow'   => '<td class="%6$s">%4$s</td>',
    );

    /**
     * @var array
     */
    protected $bootstrapTemplates = array(
        'help'          => '<%1$s class="help-%2$s">%3$s</%1$s>',
        'prependAppend' => '<div class="%1$s">%2$s%3$s%4$s</div>',
    );

    /**
     * @var array
     */
    protected $labelAttributes = array();

    /**
     * @var FormLabel
     */
    protected $labelHelper;

    /**
     * @var FormElement
     */
    protected $elementHelper;

    /**
     * @var FormElementErrors
     */
    protected $elementErrorsHelper;

    /**
     * @var array
     */
    protected $groupElements = array(
        'multi_checkbox',
        'multicheckbox',
        'radio',
    );

    /**
     * @var array
     */
    protected $compactFormStyles = array(
        'inline',
        'search',
        'tableRow',
    );

    /**
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $escapeHtmlHelper    = $this->getEscapeHtmlHelper();
        $labelHelper         = $this->getLabelHelper();
        $elementHelper       = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();
        $label               = $element->getLabel();

        $elementErrorsHelper->setMessageOpenFormat('<div%s>')
            ->setMessageSeparatorString('<br>')
            ->setMessageCloseString('</div>');

        $elementErrors   = $elementErrorsHelper->render($element, array('class' => 'help-block'));

        $elementStatus    = $this->getElementStatus($element);
        $type             = $element->getAttribute('type');
        $bootstrapOptions = $element->getOption('bootstrap');
        $formStyle        = (isset($bootstrapOptions['style'])) ? $bootstrapOptions['style'] : $this->getFormStyle();

        $labelOpen       = '';
        $labelClose      = '';
        $labelAttributes = '';
        $markup          = '';

        if ($type == 'hidden') {
            if ($formStyle != "tableHead") {
                $markup .= $elementHelper->render($element);
                $markup .= $elementErrorsHelper->render($element, array('class' => 'alert alert-error'));
            }
        } else {
            if (!empty($label)) {
                if (in_array($formStyle, $this->compactFormStyles)) {
                    $element->setAttribute('placeholder', $label);
                } else {

                    $label           = $escapeHtmlHelper($label);
                    $labelAttributes = $element->getLabelAttributes();

                    if (empty($labelAttributes)) {
                        $labelAttributes = $this->labelAttributes;
                    }

                    $labelAttributes['class'] = isset($labelAttributes['class']) ? $labelAttributes['class'] . ' control-label' : 'control-label';

                    $labelOpen  = $labelHelper->openTag($labelAttributes);
                    $labelClose = $labelHelper->closeTag();
                }
            }

            if (in_array($type, $this->groupElements)) {
                $options = $element->getValueOptions();
                foreach ($options as $key => $optionSpec) {
                    if (is_string($optionSpec)) {
                        $tVal                                       = $options[$key];
                        $options[$key]                              = array();
                        $options[$key]['label']                     = $tVal;
                        $options[$key]['value']                     = $key;
                        $options[$key]['label_attributes']['class'] = ($type == 'radio') ? 'radio' : 'checkbox';
                        $options[$key]['label_attributes']['class'] .= (in_array($formStyle, $this->compactFormStyles)) ? ' inline' : null;
                    } else {
                        $options[$key]['label_attributes']['class'] = ($type == 'radio') ? 'radio' : 'checkbox';
                        $options[$key]['label_attributes']['class'] .= (in_array($formStyle, $this->compactFormStyles)) ? ' inline' : null;
                    }
                }
                $element->setAttribute('value_options', $options);
            }

            $elementString = $elementHelper->render($element);

            $elementString = $this->renderBootstrapOptions($elementString, $bootstrapOptions);

            $markup = sprintf(
                $this->defaultElementTemplates[$formStyle],
                $labelOpen,
                $label,
                $labelClose,
                $elementString,
                $elementErrors,
                $elementStatus
            );
        }

        return $markup;
    }

    /**
     * Proxies to {@link render()}.
     *
     * @param  null|ElementInterface $element
     * @param  null|string           $labelPosition
     * @return string|FormRow
     */
    public function __invoke(
        ElementInterface $element = null,
        $formStyle = 'horizontal',
        $labelPosition = null,
        $renderErrors = true
    ) {
        if (!$element) {
            return $this;
        }

        $this->setFormStyle($formStyle);

        if ($labelPosition !== null) {
            $this->setLabelPosition($labelPosition);
        }

        $this->setRenderErrors($renderErrors);

        return $this->render($element);
    }

    /**
     * @param  string  $style
     * @return FormRow
     */
    public function setFormStyle($style)
    {
        $this->formStyle = $style;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormStyle()
    {
        return $this->formStyle;
    }

    /**
     * @param  ElementInterface $element
     * @return string
     */
    public function getElementStatus(ElementInterface $element)
    {
        $status = '';
        if (count($element->getMessages())) {
            $status = ' error ';
        }

        return $status;
    }

    /**
     * @param  string      $template
     * @return NULL|string
     */
    public function getDefaultElementTemplate($style)
    {
        if (!isset($this->defaultElementTemplates[$style])) {
            return null;
        }

        return $this->defaultElementTemplates[$style];
    }

    /**
     * @param string $style
     * @param string $template
     * @return $this
     */
    public function setDefaultElementTemplate($style)
    {
        $this->defaultElementTemplates[$style];

        return $this;
    }

    /**
     * @param string            $elementString
     * @param array|Traversable $options
     */
    public function renderBootstrapOptions($elementString, $options)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();

        if (isset($options['prepend']) || isset($options['append'])) {
            $template = $this->bootstrapTemplates['prependAppend'];
            $class    = '';
            $prepend  = '';
            $append   = '';
            if (isset($options['prepend'])) {
                $class .= 'input-prepend ';
                if (!is_array($options['prepend'])) {
                    $options['prepend'] = (array) $options['prepend'];
                }
                foreach ($options['prepend'] as $p) {
                    $prepend .= '<span class="add-on">' . $escapeHtmlHelper($p) . '</span>';
                }
            }
            if (isset($options['append'])) {
                $class .= 'input-append ';
                if (!is_array($options['append'])) {
                    $options['append'] = (array) $options['append'];
                }
                foreach ($options['append'] as $a) {
                    $append .= '<span class="add-on">' . $escapeHtmlHelper($a) . '</span>';
                }
            }

            $elementString = sprintf($template, $class, $prepend, $elementString, $append);
        }
        if (isset($options['help'])) {
            $help     = $options['help'];
            $template = $this->bootstrapTemplates['help'];
            $style    = 'inline';
            $content  = '';
            if (is_array($help)) {
                if (isset($help['style'])) {
                    $style = $help['style'];
                }
                if (isset($help['content'])) {
                    $content    = $help['content'];
                    if (null !== ($translator = $this->getTranslator())) {
                        $content = $translator->translate(
                            $content,
                            $this->getTranslatorTextDomain()
                        );
                    }
                }
            } else {

                $content = $help;
            }

            $tag = $style == 'block' ? 'p' : 'span';

            $elementString .= sprintf(
                $template,
                $tag,
                $style,
                $content
            );
        }

        return $elementString;
    }

    /**
     * @return FormElement
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (!$this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        return $this->elementHelper;
    }
}
