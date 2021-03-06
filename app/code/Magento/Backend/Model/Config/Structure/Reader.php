<?php
/**
 * Backend System Configuration reader.
 * Retrieves system configuration form layout from system.xml files. Merges configuration and caches it.
 *
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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Backend\Model\Config\Structure;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/system/tab' => 'id',
        '/config/system/section' => 'id',
        '/config/system/section/group' => 'id',
        '/config/system/section/group/field' => 'id',
        '/config/system/section/group/field/depends/field' => 'id',
        '/config/system/section/group/group' => 'id',
        '/config/system/section/group/group/field' => 'id',
        '/config/system/section/group/group/field/depends/field' => 'id',
        '/config/system/section/group/group/group' => 'id',
        '/config/system/section/group/group/group/field' => 'id',
        '/config/system/section/group/group/group/field/depends/field' => 'id',
        '/config/system/section/group/group/group/group' => 'id',
        '/config/system/section/group/group/group/group/field' => 'id',
        '/config/system/section/group/group/group/group/field/depends/field' => 'id',
        '/config/system/section/group/group/group/group/group' => 'id',
        '/config/system/section/group/group/group/group/group/field' => 'id',
        '/config/system/section/group/group/group/group/group/field/depends/field' => 'id',
        '/config/system/section/group/field/options/option' => 'label',
        '/config/system/section/group/group/field/options/option' => 'label',
        '/config/system/section/group/group/group/field/options/option' => 'label',
        '/config/system/section/group/group/group/group/field/options/option' => 'label',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Backend\Model\Config\Structure\Converter $converter
     * @param \Magento\Backend\Model\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Backend\Model\Config\Structure\Converter $converter,
        \Magento\Backend\Model\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'system.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
