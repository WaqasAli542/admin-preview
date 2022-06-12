<?php

namespace WMZ\AdminPreview\Block\Adminhtml\CMS\Page;

use Magento\Cms\Model\Page;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\Request\Http;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use WMZ\AdminPreview\Helper\Data;

class CMSPagePreview implements ButtonProviderInterface
{
    /**
     * Admin Preview ACL Resource
     */
    private const ADMIN_RESOURCE_NAME = 'WMZ_AdminPreview::config';

    /**
     * @var Http
     */
    private $request;

    /**
     * @var Page
     */
    private $cmsPage;

    /**
     * @var Data
     */
    private $adminPreviewHelper;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductMetadata
     */
    private $productMetaData;

    /**
     * CMSPagePreview constructor.
     * @param Http $request
     * @param Page $cmsPage
     * @param Data $adminPreviewHelper
     * @param AuthorizationInterface $authorization
     * @param StoreManagerInterface $storeManager
     * @param ProductMetadata $productMetaData
     */
    public function __construct(
        Http $request,
        Page $cmsPage,
        Data $adminPreviewHelper,
        AuthorizationInterface $authorization,
        StoreManagerInterface $storeManager,
        ProductMetadata $productMetaData
    ) {
        $this->request = $request;
        $this->cmsPage = $cmsPage;
        $this->adminPreviewHelper = $adminPreviewHelper;
        $this->authorization = $authorization;
        $this->storeManager = $storeManager;
        $this->productMetaData = $productMetaData;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getButtonData(): array
    {
        $cmsPageId = $this->request->getParam('page_id');
        $page = $this->cmsPage->load($cmsPageId);
        if ($this->cmsPage->load($cmsPageId)->isActive() == true
            && $this->authorization->isAllowed(
                self::ADMIN_RESOURCE_NAME
            ) === true
            && $this->adminPreviewHelper->checkCmsPreviewIsEnable() == true
        ) {
            return [
                'label' => __('Preview'),
                'on_click' => sprintf(
                    "window.open('%s')",
                    $this->getCmsPageUrl($page)
                ),
                'class' => '',
                'sort_order' => 100
            ];
        }
        return [];
    }

    /**
     * @param $page
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCmsPageUrl($page): string
    {
        $storeId = $page->getStoreId()[0];
        $identifier = $page->getIdentifier();
        $url = $this->storeManager->getStore()->getBaseUrl() . $identifier;
        if ($this->productMetaData->getVersion() < '2.3.0') {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
            $url .= '?___store=' . $storeCode;
        }
        return $url;
    }
}
