<?php
declare(strict_types=1);

namespace Punchout2Go\Punchout\Model\Transfer\CustomFields\Parts;

use Magento\Quote\Api\Data\CartItemInterface;
use Punchout2Go\Punchout\Model\Transfer\CustomFields\CartItemPartInterface;

/**
 * Class Item
 * @package Punchout2Go\Punchout\Model\Transfer\CustomFields\Parts
 */
class Item implements CartItemPartInterface
{
    /**
     * @param CartItemInterface $product
     * @param string $path
     * @return string
     */
    public function handle(CartItemInterface $product, string $path): string
    {
        $productData = $product->getData($path);
        return $product->getData($path) ?? '';
    }
}
