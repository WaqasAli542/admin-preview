<?php

namespace WMZ\AdminPreview\Block\Adminhtml\Category\Edit\Button;

use Magento\Catalog\Helper\Data;
use Magento\Framework\App\Request\Http;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use WMZ\AdminPreview\Helper\Data as AdminPreviewHelper;
use WMZ\AdminPreview\Plugin\FrontendUrl;

class CategoryPreview implements ButtonProviderInterface
{
    /**
     * Admin Preview ACL Resource
     */
    private const ADMIN_RESOURCE_NAME = 'WMZ_AdminPreview::config';

    /**
     * Admin Preview Category Controller Path
     */
    private const CATEGORY_PREVIEW_PATH = 'adminpreview/preview/category';

    /**
     * @var Http
     */
    private $request;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var AdminPreviewHelper
     */
    private $adminPreviewHelper;

    /**
     * @var FrontendUrl
     */
    private $frontendUrl;

    /**
     * CategoryPreview constructor.
     * @param Http $request
     * @param Data $helperData
     * @param AuthorizationInterface $authorization
     * @param AdminPreviewHelper $adminPreviewHelper
     * @param FrontendUrl $frontendUrl
     */
    public function __construct(
        Http $request,
        Data $helperData,
        AuthorizationInterface $authorization,
        AdminPreviewHelper $adminPreviewHelper,
        FrontendUrl $frontendUrl
    ) {
        $this->request = $request;
        $this->helperData = $helperData;
        $this->authorization = $authorization;
        $this->adminPreviewHelper = $adminPreviewHelper;
        $this->frontendUrl = $frontendUrl;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $storeId = $this->request->getParam('store');
        $categoryId = $this->request->getParam('id');
        $currentCategory = $this->helperData->getCategory();
        if ($this->adminPreviewHelper->checkCategoryPreviewIsEnable() == true
            && $this->authorization->isAllowed(
                self::ADMIN_RESOURCE_NAME
            ) === true
            && is_object($currentCategory) === true
            && $currentCategory->getIsActive() == true
            && $currentCategory->getUrlKey() !== null
        ) {
            return [
                'label' => __('Preview'),
                'on_click' => sprintf(
                    "window.open('%s')",
                    $this->getProductUrl($categoryId, $storeId)
                ),
                'class' => '',
                'sort_order' => 10
            ];
        }
        return [];
    }

    /**
     * @param $categoryId
     * @param $storeId
     * @return string
     */
    public function getProductUrl($categoryId, $storeId): string
    {
        return $this->frontendUrl->getFrontendUrl()
            ->getUrl(
                self::CATEGORY_PREVIEW_PATH,
                ['category_id' => $categoryId, 'store' => $storeId]
            );
    }
}
