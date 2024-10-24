<?php
declare(strict_types=1);

namespace Punchout2Go\Punchout\Model\Transfer\QuoteItemTransferData\Fields;

use Magento\Quote\Api\Data\CartItemInterface;
use Punchout2Go\Punchout\Model\Transfer\QuoteItemTransferData\QuoteItemRelatedDataHandlerInterface;

/**
 * Class CustomFields
 * @package Punchout2Go\Punchout\Model\Transfer\QuoteItemTransferData\Fields
 */
class QuoteCustomFields implements QuoteItemRelatedDataHandlerInterface  
{
    /**
     * @var \Punchout2Go\Punchout\Helper\Transfer
     */
    protected $helper;

    /**
     * @var \Punchout2Go\Punchout\Helper\Data
     */
    protected $defaultHelper;

    /**
     * @var \Punchout2Go\Punchout\Model\Transfer\CustomFields\CartItemPartResolver
     */
    protected $partFactory;
	
    /**
     * @var \Punchout2Go\Punchout\Api\LoggerInterface
     */
    protected $logger;


    /**
     * QuoteCustomFields constructor.
     * @param \Punchout2Go\Punchout\Helper\Transfer $helper
     * @param \Punchout2Go\Punchout\Helper\Data $data
     * @param \Punchout2Go\Punchout\Model\Transfer\CustomFields\CartItemPartResolver $partFactory
	 * @param \Punchout2Go\Punchout\Api\LoggerInterface $logger
     */
    public function __construct(
        \Punchout2Go\Punchout\Helper\Transfer $helper,
        \Punchout2Go\Punchout\Helper\Data $data,
        \Punchout2Go\Punchout\Model\Transfer\CustomFields\CartItemPartResolver $partFactory,
		\Punchout2Go\Punchout\Api\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->defaultHelper = $data;
        $this->partFactory = $partFactory;
		$this->logger = $logger;
    }

    /**
     * @param CartItemInterface $product
     * @param null $storeId
     * @return mixed[]
     */
    public function handle(CartItemInterface $product, $storeId): array
    {
        $result = [];
        $fields = $this->helper->getCartItemMap();		
        if (!$fields) {
            return $result;
        }
        foreach ($fields as $field) {
            list($source, $destination) = $this->defaultHelper->prepareSource($field);
            if (strlen($source) && strlen($destination) && ($val = $this->getMapSourceValue($source, $product))) {		
                $result[$destination] = $val;
            }
        }		
        return $result;
    }

    /**
     * @param string $path
     * @param CartItemInterface $product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getMapSourceValue(string $path, CartItemInterface $product)
    {
        $s = [];
        if (!preg_match('/^([^:]+):([^:]+)$/', $path, $s)) {
            return '';
        }
        $part = $s[1];
        $path = $s[2];

//		if ($product->getData($path)) {
//            return $product->getData($path);
//        }
//        return '';

        $handler = $this->partFactory->resolve($part);	
        if (!$handler) {		
            return '';
        }		
        return $handler->handle($product, $path, $part);
    }
}
