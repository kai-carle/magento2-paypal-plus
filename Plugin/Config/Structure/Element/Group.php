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
 * @author   Matteo Bertozzi <bertozzi@i-ways.net>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License 3.0
 * @link     https://www.i-ways.net
 */

namespace Iways\PayPalPlus\Plugin\Config\Structure\Element;

use Iways\PayPalPlus\Helper\Data;
use Magento\Config\Model\Config\Structure\Element\Group as OriginalGroup;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Store\Model\ScopeInterface;

class Group
{
    const CONFIG_GROUP_ID = 'third_party_modul_info';

    /**
     * Protected $helper
     *
     * @var \Iways\PayPalPlus\Helper\Data
     */
    protected $helper;

    /**
     * Protected $paymentConfig
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    public function __construct(
        Data $helper,
        PaymentConfig $paymentConfig
    ) {
        $this->helper = $helper;
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Adds dynamic configuration fields if third party methods are set as in use
     *
     * @param defaultSection $subject
     * @param callable $proceed
     * @param array $data
     * @param $scope
     *
     * @return mixed
     */
    public function aroundSetData(OriginalGroup $subject, callable $proceed, array $data, $scope)
    {
        $fields = [];

        if ($data['id'] == self::CONFIG_GROUP_ID) {
            if ($thirdPartyModuls = $this->helper->getPaymentThirdPartyModuls()) {
                $activePaymentMethods = $this->paymentConfig->getActiveMethods();
                $path = "payment/iways_paypalplus_section/third_party_modul_info";
                foreach (explode(',', $thirdPartyModuls ?? '') as $key => $value) {
                    if (isset($activePaymentMethods[$value])) {
                        $paymentMethod = $activePaymentMethods[$value];

                        $id = 'text_' . $value;
                        $fields[$id] = [
                            'id' => $id,
                            'type' => 'text',
                            'label' => $paymentMethod->getTitle(),
                            'sortOrder' => $key * 10,
                            'showInDefault' => "1",
                            'showInWebsite' => "1",
                            'showInStore' => "1",
                            'path' => $path,
                            'config_path' => $path . '_' . $id,
                            '_elementType' => "field"
                        ];

                        $id = 'image_' . $value;
                        $fields[$id] = [
                            'id' => $id,
                            'type' => 'text',
                            'label' => __("Custom optional image for ")
                                     . '<br /><small>"' . $paymentMethod->getTitle() . '"<small>',
                            'sortOrder' => $key * 10 + 5,
                            'showInDefault' => "1",
                            'showInWebsite' => "1",
                            'showInStore' => "1",
                            'path' => $path,
                            'config_path' => $path . '_' . $id,
                            '_elementType' => "field"
                        ];
                    }
                }
            }

            if (!empty($fields)) {
                $data['children'] = $fields;
            }
        }

        return $proceed($data, $scope);
    }
}
