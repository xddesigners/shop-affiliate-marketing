<?php

namespace XD\Shop\AffiliateMarketing\Extensions;

use DNADesign\Elemental\TopPage\DataExtension;
use SilverShop\Model\Order;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use XD\Shop\AffiliateMarketing\Exceptions\PostbackFailedException;
use XD\Shop\AffiliateMarketing\Providers\AffiliateProvider;

/**
 * class OrderExtension
 * @property Order $owner
 */
class OrderExtension extends DataExtension
{
    private static $db = [
        'AffiliateMarketingTransactionID' => 'Text'
    ];

    public function onPlaceOrder()
    {
        if (($controller = Controller::curr()) && $request = $controller->getRequest()) {
            $provider = Injector::inst()->create('AffiliateProvider');
            $provider->storeTransactionID($request, $this->owner);
        }
    }

    public function onPaid()
    {
        if (($controller = Controller::curr()) && $request = $controller->getRequest()) {
            try {
                /** @var AffiliateProvider $provider */
                $provider = Injector::inst()->create('AffiliateProvider');
                $provider->doPostBack($request, $this->owner);
            } catch (PostbackFailedException $e) {
                // soft exceptions so we don't interupt the order process
            }
        }
    }
}
