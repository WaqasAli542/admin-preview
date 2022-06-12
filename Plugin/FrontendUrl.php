<?php

namespace WMZ\AdminPreview\Plugin;

use Magento\Framework\UrlInterface;

class FrontendUrl
{
    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * FrontendUrl constructor.
     * @param UrlInterface $urlInterface
     */
    public function __construct(UrlInterface $urlInterface)
    {
        $this->urlInterface = $urlInterface;
    }

    /**
     * @return UrlInterface
     */
    public function getFrontendUrl(): UrlInterface
    {
        return $this->urlInterface;
    }
}
