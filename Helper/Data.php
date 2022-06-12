<?php

namespace WMZ\AdminPreview\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Product Config Path
     */
    private const ADMIN_PRODUCT_PREVIEW = 'admin_preview/general/product';

    /**
     * Category Config Path
     */
    private const ADMIN_CATEGORY_PREVIEW = 'admin_preview/general/category';

    /**
     * CMS Pages Config Path
     */
    private const ADMIN_CMS_PREVIEW = 'admin_preview/general/cms';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function checkProductPreviewIsEnable($store = null)
    {
        return $this->scopeConfig->getValue(
            self::ADMIN_PRODUCT_PREVIEW,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function checkCategoryPreviewIsEnable($store = null)
    {
        return $this->scopeConfig->getValue(
            self::ADMIN_CATEGORY_PREVIEW,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function checkCmsPreviewIsEnable($store = null)
    {
        return $this->scopeConfig->getValue(
            self::ADMIN_CMS_PREVIEW,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
