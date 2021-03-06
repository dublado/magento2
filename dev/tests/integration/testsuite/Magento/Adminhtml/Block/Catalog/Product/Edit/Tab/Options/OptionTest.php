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
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options;

/**
 * @magentoAppArea adminhtml
 */
class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOptionValuesCaching()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Option');
        /** @var $productWithOptions \Magento\Catalog\Model\Product */
        $productWithOptions = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $productWithOptions->setTypeId('simple')
            ->setId(1)
            ->setAttributeSetId(4)
            ->setWebsiteIds(array(1))
            ->setName('Simple Product With Custom Options')
            ->setSku('simple')
            ->setPrice(10)

            ->setMetaTitle('meta title')
            ->setMetaKeyword('meta keyword')
            ->setMetaDescription('meta description')

            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED);

        $product = clone $productWithOptions;
        /** @var $option \Magento\Catalog\Model\Product\Option */
        $option = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product\Option',
            array('data' => array('id' => 1, 'title' => 'some_title'))
        );
        $productWithOptions->addOption($option);

        $block->setProduct($productWithOptions);
        $this->assertNotEmpty($block->getOptionValues());

        $block->setProduct($product);
        $this->assertNotEmpty($block->getOptionValues());

        $block->setIgnoreCaching(true);
        $this->assertEmpty($block->getOptionValues());
    }
}
