<?php

namespace WMZ\AdminPreview\Controller\Adminhtml\Preview;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Category implements ActionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * Category constructor.
     * @param RequestInterface $request
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param ResultFactory $resultFactory
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        RequestInterface $request,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager,
        ResultFactory $resultFactory,
        ProductMetadataInterface $productMetadata
    ) {
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $categoryId = $this->request->getParam('category_id');
        $storeId = $this->request->getParam('store');
        $category = $this->categoryRepository->get($categoryId);
        if ($storeId) {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
            $categoryUrl = strtok(
                $category->getUrl(),
                '?'
            ) . '?___store=' . $storeCode;
        } else {
            $categoryUrl = strtok($category->getUrl(), '?');
            if ($this->productMetadata->getVersion() < '2.3.0') {
                $storeCode = $this->storeManager->getStore('0')->getCode();
                $categoryUrl .= '?___store=' . $storeCode;
            }
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($categoryUrl);
        return $resultRedirect;
    }
}
