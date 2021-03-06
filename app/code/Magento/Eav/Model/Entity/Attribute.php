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
 * @package     Magento_Eav
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV Entity attribute model
 *
 * @method \Magento\Eav\Model\Entity\Attribute setOption($value)
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity;

class Attribute extends \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix                         = 'eav_entity_attribute';

    CONST ATTRIBUTE_CODE_MAX_LENGTH                 = 30;

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getAttribute() in this case
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    const CACHE_TAG         = 'EAV_ATTRIBUTE';
    protected $_cacheTag    = 'EAV_ATTRIBUTE';

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_catalogProductFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Catalog\Model\ProductFactory $catalogProductFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Catalog\Model\ProductFactory $catalogProductFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $coreData,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_locale = $locale;
        $this->_catalogProductFactory = $catalogProductFactory;
    }

    /**
     * Retrieve default attribute backend model by attribute code
     *
     * @return string
     */
    protected function _getDefaultBackendModel()
    {
        switch ($this->getAttributeCode()) {
            case 'created_at':
                return 'Magento\Eav\Model\Entity\Attribute\Backend\Time\Created';

            case 'updated_at':
                return 'Magento\Eav\Model\Entity\Attribute\Backend\Time\Updated';

            case 'store_id':
                return 'Magento\Eav\Model\Entity\Attribute\Backend\Store';

            case 'increment_id':
                return 'Magento\Eav\Model\Entity\Attribute\Backend\Increment';
        }

        return parent::_getDefaultBackendModel();
    }

    /**
     * Retrieve default attribute frontend model
     *
     * @return string
     */
    protected function _getDefaultFrontendModel()
    {
        return parent::_getDefaultFrontendModel();
    }

    /**
     * Retrieve default attribute source model
     *
     * @return string
     */
    protected function _getDefaultSourceModel()
    {
        if ($this->getAttributeCode() == 'store_id') {
            return 'Magento\Eav\Model\Entity\Attribute\Source\Store';
        }
        return parent::_getDefaultSourceModel();
    }

    /**
     * Delete entity
     *
     * @return \Magento\Eav\Model\Resource\Entity\Attribute
     */
    public function deleteEntity()
    {
        return $this->_getResource()->deleteEntity($this);
    }

    /**
     * Load entity_attribute_id into $this by $this->attribute_set_id
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function loadEntityAttributeIdBySet()
    {
        // load attributes collection filtered by attribute_id and attribute_set_id
        $filteredAttributes = $this->getResourceCollection()
            ->setAttributeSetFilter($this->getAttributeSetId())
            ->addFieldToFilter('entity_attribute.attribute_id', $this->getId())
            ->load();
        if (count($filteredAttributes) > 0) {
            // getFirstItem() can be used as we can have one or zero records in the collection
            $this->setEntityAttributeId($filteredAttributes->getFirstItem()->getEntityAttributeId());
        }
        return $this;
    }

    /**
     * Prepare data for save
     *
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    protected function _beforeSave()
    {
        // prevent overriding product data
        if (isset($this->_data['attribute_code'])
            && $this->_catalogProductFactory->create()->isReservedAttribute($this)
        ) {
            throw new \Magento\Eav\Exception(__('The attribute code \'%1\' is reserved by system. Please try another attribute code', $this->_data['attribute_code']));
        }

        /**
         * Check for maximum attribute_code length
         */
        if(isset($this->_data['attribute_code']) &&
           !\Zend_Validate::is($this->_data['attribute_code'],
                              'StringLength',
                              array('max' => self::ATTRIBUTE_CODE_MAX_LENGTH))
        ) {
            throw new \Magento\Eav\Exception(__('Maximum length of attribute code must be less than %1 symbols', self::ATTRIBUTE_CODE_MAX_LENGTH));
        }

        $defaultValue   = $this->getDefaultValue();
        $hasDefaultValue = ((string)$defaultValue != '');

        if ($this->getBackendType() == 'decimal' && $hasDefaultValue) {
            if (!\Zend_Locale_Format::isNumber($defaultValue,
                                              array('locale' => $this->_locale->getLocaleCode()))
            ) {
                throw new \Magento\Eav\Exception(__('Invalid default decimal value'));
            }

            try {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    array('locale' => $this->_locale->getLocaleCode())
                );
                $this->setDefaultValue($filter->filter($defaultValue));
            } catch (\Exception $e) {
                throw new \Magento\Eav\Exception(__('Invalid default decimal value'));
            }
        }

        if ($this->getBackendType() == 'datetime') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('Magento\Eav\Model\Entity\Attribute\Backend\Datetime');
            }

            if (!$this->getFrontendModel()) {
                $this->setFrontendModel('Magento\Eav\Model\Entity\Attribute\Frontend\Datetime');
            }

            // save default date value as timestamp
            if ($hasDefaultValue) {
                $format = $this->_locale->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
                try {
                    $defaultValue = $this->_locale->date($defaultValue, $format, null, false)->toValue();
                    $this->setDefaultValue($defaultValue);
                } catch (\Exception $e) {
                    throw new \Magento\Eav\Exception(__('Invalid default date'));
                }
            }
        }

        if ($this->getBackendType() == 'gallery') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend');
            }
        }

        return parent::_beforeSave();
    }

    /**
     * Save additional data
     *
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    protected function _afterSave()
    {
        $this->_getResource()->saveInSetIncluding($this);
        return parent::_afterSave();
    }

    /**
     * Detect backend storage type using frontend input type
     *
     * @param string $type frontend_input field value
     * @return string backend_type field value
     */
    public function getBackendTypeByInput($type)
    {
        $field = null;
        switch ($type) {
            case 'text':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
                $field = 'varchar';
                break;

            case 'image':
            case 'textarea':
                $field = 'text';
                break;

            case 'date':
                $field = 'datetime';
                break;

            case 'select':
            case 'boolean':
                $field = 'int';
                break;

            case 'price':
            case 'weight':
                $field = 'decimal';
                break;
        }

        return $field;
    }

    /**
     * Detect default value using frontend input type
     *
     * @param string $type frontend_input field name
     * @return string default_value field value
     */
    public function getDefaultValueByInput($type)
    {
        $field = '';
        switch ($type) {
            case 'select':
            case 'gallery':
            case 'media_image':
                break;
            case 'multiselect':
                $field = null;
                break;

            case 'text':
            case 'price':
            case 'image':
            case 'weight':
                $field = 'default_value_text';
                break;

            case 'textarea':
                $field = 'default_value_textarea';
                break;

            case 'date':
                $field = 'default_value_date';
                break;

            case 'boolean':
                $field = 'default_value_yesno';
                break;
        }

        return $field;
    }

    /**
     * Retrieve attribute codes by frontend type
     *
     * @param string $type
     * @return array
     */
    public function getAttributeCodesByFrontendType($type)
    {
        return $this->getResource()->getAttributeCodesByFrontendType($type);
    }

    /**
     * Return array of labels of stores
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->getData('store_labels')) {
            $storeLabel = $this->getResource()->getStoreLabelsByAttributeId($this->getId());
            $this->setData('store_labels', $storeLabel);
        }
        return $this->getData('store_labels');
    }

    /**
     * Return store label of attribute
     *
     * @return string
     */
    public function getStoreLabel($storeId = null)
    {
        if ($this->hasData('store_label')) {
            return $this->getData('store_label');
        }
        $store = $this->_storeManager->getStore($storeId);
        $label = false;
        if (!$store->isAdmin()) {
            $labels = $this->getStoreLabels();
            if (isset($labels[$store->getId()])) {
                return $labels[$store->getId()];
            }
        }
        return $this->getFrontendLabel();
    }

    /**
     * Get attribute sort weight
     *
     * @param int $setId
     * @return float
     */
    public function getSortWeight($setId)
    {
        $groupSortWeight = isset($this->_data['attribute_set_info'][$setId]['group_sort'])
            ? (float) $this->_data['attribute_set_info'][$setId]['group_sort'] * 1000
            : 0.0;
        $sortWeight = isset($this->_data['attribute_set_info'][$setId]['sort'])
            ? (float)$this->_data['attribute_set_info'][$setId]['sort'] * 0.0001
            : 0.0;
        return $groupSortWeight + $sortWeight;
    }
}
