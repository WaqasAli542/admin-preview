<?php

namespace WMZ\AdminPreview\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Helper\Data;
use Magento\Framework\App\Request\Http;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use WMZ\AdminPreview\Helper\Data as AdminPreviewHelper;
use WMZ\AdminPreview\Plugin\FrontendUrl;

class ProductPreview implements ButtonProviderInterface
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
     * @var Http
     */
    private $request;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var FrontendUrl
     */
    private $frontendUrl;

    /**
     * @var AdminPreviewHelper
     */
    private $adminPreviewHelper;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * ProductPreview constructor.
     * @param Http $request
     * @param Data $helperData
     * @param FrontendUrl $frontendUrl
     * @param AdminPreviewHelper $adminPreviewHelper
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Http $request,
        Data $helperData,
        FrontendUrl $frontendUrl,
        AdminPreviewHelper $adminPreviewHelper,
        AuthorizationInterface $authorization
    ) {
        $this->request = $request;
        $this->helperData = $helperData;
        $this->frontendUrl = $frontendUrl;
        $this->adminPreviewHelper = $adminPreviewHelper;
        $this->authorization = $authorization;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $storeId = $this->request->getParam('store');
        $productId = $this->request->getParam('id');
        $currentProduct = $this->helperData->getProduct();
        if ($this->adminPreviewHelper->checkProductPreviewIsEnable() == true
            && $currentProduct->getStatus() == true
            && $this->authorization->isAllowed(
                self::ADMIN_RESOURCE_NAME
            ) === true
            && $currentProduct->getVisibility() > 1
        ) {
            return [
                'label' => __('Preview'),
                'on_click' => sprintf(
                    "window.open('%s')",
                    $this->getProductUrl($productId, $storeId)
                ),
                'sort_order' => 10
            ];
        }
        return [];
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
