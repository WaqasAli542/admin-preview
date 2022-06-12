<?php

namespace WMZ\AdminPreview\Plugin\Adminhtml\Catalog\Grid;

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Ui\Component\Listing\Columns\ProductActions as MageProductAction;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use WMZ\AdminPreview\Helper\Data;
use WMZ\AdminPreview\Plugin\FrontendUrl;

class ProductActions
{
    /**
     * Admin Preview ACL Resource
     */
    private const ADMIN_RESOURCE_NAME = 'WMZ_AdminPreview::config';

    /**
     * Admin Preview Product Controller Path
     */
    private const PRODUCT_PREVIEW_PATH = 'adminpreview/preview/product';

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var UrlInterface
     */
    private $frontendUrlBuilder;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var FrontendUrl
     */
    private $frontendUrl;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * ProductActions constructor.
     * @param ContextInterface $context
     * @param UrlInterface $frontendUrlBuilder
     * @param AuthorizationInterface $authorization
     * @param ProductRepository $productRepository
     * @param FrontendUrl $frontendUrl
     * @param Data $helperData
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $frontendUrlBuilder,
        AuthorizationInterface $authorization,
        ProductRepository $productRepository,
        FrontendUrl $frontendUrl,
        Data $helperData
    ) {
        $this->context = $context;
        $this->frontendUrlBuilder = $frontendUrlBuilder;
        $this->authorization = $authorization;
        $this->productRepository = $productRepository;
        $this->frontendUrl = $frontendUrl;
        $this->helperData = $helperData;
    }

    /**
     * @param MageProductAction $subject
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterPrepareDataSource(
        MageProductAction $subject,
        array $dataSource
    ): array {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            $this->frontendUrlBuilder->setScope($storeId);
            if ($this->helperData->checkProductPreviewIsEnable() == 1
                && $this->authorization->isAllowed(
                    self::ADMIN_RESOURCE_NAME
                )
            ) {
                foreach ($dataSource['data']['items'] as $key => $item) {
                    $product = $this->productRepository->getById((int)$item['entity_id']);
                    if ($product->getStatus() == 1
                        && $product->getVisibility() > 1
                    ) {
                        $dataSource['data']['items'][$key]
                        [$subject->getData('name')]['preview'] = [
                            'href' => $this->getProductUrl(
                                $item['entity_id'],
                                $storeId
                            ),
                            'target' => '_blank',
                            'label' => __('Preview'),
                            'hidden' => false,
                        ];
                    }
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $productId
     * @param $storeId
     * @return string
     */
    public function getProductUrl($productId, $storeId): string
    {
        return $this->frontendUrl->getFrontendUrl()
            ->getUrl(
                self::PRODUCT_PREVIEW_PATH,
                ['product_id' => $productId, 'store' => $storeId]
            );
    }
}
