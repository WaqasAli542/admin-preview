<?php

namespace WMZ\AdminPreview\Controller\Adminhtml\Preview;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Product implements ActionInterface
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
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * Product constructor.
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param ProductMetadataInterface $productMetadata
     * @param ProductRepository $productRepository
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ProductMetadataInterface $productMetadata,
        ProductRepository $productRepository,
        ResultFactory $resultFactory
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
        $this->productRepository = $productRepository;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $productId = $this->request->getParam('product_id');
        $storeId = $this->request->getParam('store');
        $product = $this->productRepository->getById($productId);
        if ($storeId) {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
            $productUrl = strtok(
                $product->setStoreId($storeId)->getUrlInStore(),
                '?'
            )
                . '?___store=' . $storeCode;
        } else {
            $storeId = '1';
            $productUrl = strtok(
                $product->setStoreId($storeId)->getUrlInStore(),
                '?'
            );
            if ($this->productMetadata->getVersion() < '2.3.0') {
                $storeCode = $this->storeManager->getStore('0')->getCode();
                $productUrl .= '?___store=' . $storeCode;
            }
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($productUrl);
        return $resultRedirect;
    }
}
