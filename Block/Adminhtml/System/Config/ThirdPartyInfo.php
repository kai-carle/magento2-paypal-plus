<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 7.3.17
 *
 * @category Modules
 * @package  Magento
 * @author   Robert Hillebrand <hillebrand@i-ways.net>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License 3.0
 * @link     https://www.i-ways.net
 */

/**
 * Config form fieldset renderer
 */
namespace Iways\PayPalPlus\Block\Adminhtml\System\Config;

class ThirdPartyInfo extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * Payment method config
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    public function __construct(
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        array $data = []
    ) {
        $this->paymentConfig = $paymentConfig;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);
        $dummyField = $element->getElements()[0];

        $thirdPartyModuls = $this->_scopeConfig->getValue('payment/iways_paypalplus_payment/third_party_moduls');
        $thirdPartyMethods = explode(',', $thirdPartyModuls ?? '');
        foreach ($this->paymentConfig->getActiveMethods() as $paymentMethod) {
            if (in_array($paymentMethod->getCode(), $thirdPartyMethods)) {
                $thirdPartyMethod = $paymentMethod->getCode();
                $textSetting = 'payment/iways_paypalplus_section/third_party_modul_info/text_' . $thirdPartyMethod;
                $text = $this->_scopeConfig->getValue($textSetting);
                $field = clone $dummyField;
                $field->setData('name', str_replace('dummy', $thirdPartyMethod, $field->getName()));
                $field->setData('label', $paymentMethod->getTitle());
                $field->setData('value', $text);
                $fieldConfig = $field->getData('field_config');
                $fieldConfig['id'] = 'text_' . $thirdPartyMethod;
                $fieldConfig['label'] = $paymentMethod->getTitle();
                $fieldConfig['config_path'] = $textSetting;
                $field->setData('field_config', $fieldConfig);
                $field->setData('html_id', str_replace('dummy', $thirdPartyMethod, $field->getData('html_id')));
                $html .= $field->toHtml();
            }
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
}
