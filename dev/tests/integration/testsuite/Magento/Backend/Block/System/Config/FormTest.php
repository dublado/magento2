<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Backend\Block\System\Config;

/**
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Data\Form\Factory
     */
    protected $_formFactory;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_formFactory = $this->_objectManager->create('Magento\Data\Form\Factory');
    }

    public function testDependenceHtml()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout', array('area' => 'adminhtml'));
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        /** @var $block \Magento\Backend\Block\System\Config\Form */
        $block = $layout->createBlock('Magento\Backend\Block\System\Config\Form', 'block');

        /** @var $childBlock \Magento\Core\Block\Text */
        $childBlock = $layout->addBlock('Magento\Core\Block\Text', 'element_dependence', 'block');

        $expectedValue = 'dependence_html_relations';
        $this->assertNotContains($expectedValue, $block->toHtml());

        $childBlock->setText($expectedValue);
        $this->assertContains($expectedValue, $block->toHtml());
    }

    /**
     * @covers \Magento\Backend\Block\System\Config\Form::initFields
     * @param $section \Magento\Backend\Model\Config\Structure\Element\Section
     * @param $group \Magento\Backend\Model\Config\Structure\Element\Group
     * @param $field \Magento\Backend\Model\Config\Structure\Element\Field
     * @param array $configData
     * @param bool $expectedUseDefault
     * @dataProvider initFieldsInheritCheckboxDataProvider
     */
    public function testInitFieldsUseDefaultCheckbox($section, $group, $field, array $configData, $expectedUseDefault)
    {
        $this->markTestIncomplete('MAGETWO-9058');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset($section->getId() . '_' . $group->getId(), array());

        /* @TODO Eliminate stub by proper mock / config fixture usage */
        /** @var $block \Magento\Backend\Block\System\Config\FormStub */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Backend\Block\System\Config\FormStub');
        $block->setScope(\Magento\Backend\Block\System\Config\Form::SCOPE_WEBSITES);
        $block->setStubConfigData($configData);
        $block->initFields($fieldset, $group, $section);

        $fieldsetSel = 'fieldset';
        $valueSel = sprintf('input#%s_%s_%s', $section->getId(), $group->getId(), $field->getId());
        $valueDisabledSel = sprintf('%s[disabled="disabled"]', $valueSel);
        $useDefaultSel = sprintf('input#%s_%s_%s_inherit.checkbox', $section->getId(), $group->getId(),
            $field->getId());
        $useDefaultCheckedSel = sprintf('%s[checked="checked"]', $useDefaultSel);
        $fieldsetHtml = $fieldset->getElementHtml();

        $this->assertSelectCount($fieldsetSel, true, $fieldsetHtml, 'Fieldset HTML is invalid');
        $this->assertSelectCount($valueSel, true, $fieldsetHtml, 'Field input not found in fieldset HTML');
        $this->assertSelectCount($useDefaultSel, true, $fieldsetHtml,
            '"Use Default" checkbox not found in fieldset HTML');

        if ($expectedUseDefault) {
            $this->assertSelectCount($useDefaultCheckedSel, true, $fieldsetHtml,
                '"Use Default" checkbox should be checked');
            $this->assertSelectCount($valueDisabledSel, true, $fieldsetHtml,
                'Field input should be disabled');
        } else {
            $this->assertSelectCount($useDefaultCheckedSel, false, $fieldsetHtml,
                '"Use Default" checkbox should not be checked');
            $this->assertSelectCount($valueDisabledSel, false, $fieldsetHtml,
                'Field input should not be disabled');
        }
    }


    /**
     * @covers \Magento\Backend\Block\System\Config\Form::initFields
     * @param $section \Magento\Backend\Model\Config\Structure\Element\Section
     * @param $group \Magento\Backend\Model\Config\Structure\Element\Group
     * @param $field \Magento\Backend\Model\Config\Structure\Element\Field
     * @param array $configData
     * @param bool $expectedUseDefault
     * @dataProvider initFieldsInheritCheckboxDataProvider
     * @magentoConfigFixture default/test_config_section/test_group_config_node/test_field_value config value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testInitFieldsUseConfigPath($section, $group, $field, array $configData, $expectedUseDefault)
    {
        $this->markTestIncomplete('MAGETWO-9058');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset($section->getId() . '_' . $group->getId(), array());

        /* @TODO Eliminate stub by proper mock / config fixture usage */
        /** @var $block \Magento\Backend\Block\System\Config\FormStub */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Backend\Block\System\Config\FormStub');
        $block->setScope(\Magento\Backend\Block\System\Config\Form::SCOPE_DEFAULT);
        $block->setStubConfigData($configData);
        $block->initFields($fieldset, $group, $section);

        $fieldsetSel = 'fieldset';
        $valueSel = sprintf('input#%s_%s_%s', $section->getId(), $group->getId(), $field->getId());
        $fieldsetHtml = $fieldset->getElementHtml();

        $this->assertSelectCount($fieldsetSel, true, $fieldsetHtml, 'Fieldset HTML is invalid');
        $this->assertSelectCount($valueSel, true, $fieldsetHtml, 'Field input not found in fieldset HTML');
    }

    /**
     * @TODO data provider should be static
     * @return array
     */
    public function initFieldsInheritCheckboxDataProvider()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\Core\Model\App::PARAM_BAN_CACHE => true,
        ));
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->loadAreaPart(\Magento\Core\Model\App\Area::AREA_ADMINHTML, \Magento\Core\Model\App\Area::PART_CONFIG);

        $configMock = $this->getMock('Magento\Core\Model\Config\Modules\Reader', array(), array(), '', false, false);
        $configMock->expects($this->any())->method('getConfigurationFiles')
            ->will($this->returnValue(array(__DIR__ . '/_files/test_section_config.xml')));
        $configMock->expects($this->any())->method('getModuleDir')
            ->will($this->returnValue(BP . '/app/code/Magento/Backend/etc'));

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(array(
            'Magento\Backend\Model\Config\Structure\Reader' => array(
                'parameters' => array('moduleReader' => $configMock)
            )
        ));
        /** @var \Magento\Backend\Model\Config\Structure $structure  */
        $structure = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Backend\Model\Config\Structure');

        /** @var \Magento\Backend\Model\Config\Structure\Element\Section $section  */
        $section = $structure->getElement('test_section');

        /** @var \Magento\Backend\Model\Config\Structure\Element\Group $group  */
        $group = $structure->getElement('test_section/test_group');

        /** @var \Magento\Backend\Model\Config\Structure\Element\Field $field  */
        $field = $structure->getElement('test_section/test_group/test_field');

        $fieldPath = $field->getConfigPath();

        /** @var \Magento\Backend\Model\Config\Structure\Element\Field $field  */
        $field2 = $structure->getElement('test_section/test_group/test_field_use_config');

        $fieldPath2 = $field2->getConfigPath();
        return array(
            array($section, $group, $field, array(), true),
            array($section, $group, $field, array($fieldPath => null), false),
            array($section, $group, $field, array($fieldPath => ''), false),
            array($section, $group, $field, array($fieldPath => 'value'), false),
            array($section, $group, $field2, array($fieldPath2 => 'config value'), true),
        );
    }

    public function testInitFormAddsFieldsets()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\App\ResponseInterface')->headersSentThrowsException = false;
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Controller\Front\Action',
            array(
                'request' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                    ->get('Magento\App\Request\Http'),
                'response' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                    ->get('Magento\Core\Model\App')->getResponse()
            )
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\RequestInterface')
            ->setParam('section', 'general');
        /** @var $block \Magento\Backend\Block\System\Config\Form */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Backend\Block\System\Config\Form');
        $block->initForm();
        $expectedIds = array(
            'general_country' => array(
                'general_country_default' => 'select',
                'general_country_allow' => 'select',
                'general_country_optional_zip_countries' => 'select',
                'general_country_eu_countries' => 'select'
            ),
            'general_region' => array(
                'general_region_state_required' => 'select',
                'general_region_display_all' => 'select'
            ),
            'general_locale' => array(
                'general_locale_timezone' => 'select',
                'general_locale_code' => 'select',
                'general_locale_firstday' => 'select',
                'general_locale_weekend' => 'select'
            ),
            'general_restriction' => array(
                'general_restriction_is_active' => 'select',
                'general_restriction_mode' => 'select',
                'general_restriction_http_redirect' => 'select',
                'general_restriction_cms_page' => 'select',
                'general_restriction_http_status' => 'select'
            ),
            'general_store_information' => array(
                'general_store_information_name' => 'text',
                'general_store_information_phone' => 'text',
                'general_store_information_merchant_country' => 'select',
                'general_store_information_merchant_vat_number' => 'text',
                'general_store_information_validate_vat_number' => 'text',
                'general_store_information_address' => 'textarea',
            ),
            'general_single_store_mode' => array(
                'general_single_store_mode_enabled' => 'select',
            )
        );
        $elements = $block->getForm()->getElements();
        foreach ($elements as $element) {
            /** @var $element \Magento\Data\Form\Element\Fieldset */
            $this->assertInstanceOf('Magento\Data\Form\Element\Fieldset', $element);
            $this->assertArrayHasKey($element->getId(), $expectedIds);
            $fields = $element->getElements();
            $this->assertEquals(count($expectedIds[$element->getId()]), count($fields));
            foreach ($element->getElements() as $field) {
                $this->assertArrayHasKey($field->getId(), $expectedIds[$element->getId()]);
                $this->assertEquals($expectedIds[$element->getId()][$field->getId()], $field->getType());
            }
        };
    }
}
