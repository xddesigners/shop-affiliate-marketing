<?php

namespace XD\Shop\AffiliateMarketing\Providers;

use SilverShop\Model\Order;
use SilverStripe\Control\HTTPRequest;
use XD\Shop\AffiliateMarketing\Exceptions\PostbackFailedException;

abstract class AffiliateProvider
{
    /**
     * Set the required session data from the request
     */
    abstract public function sessionFromRequest(HTTPRequest $request);

    /**
     * Handle the postback to the affiliate provider
     * Wrap any exceptions in a PostbackFailedException so these get catched properly on the order extension
     *
     * @throws PostbackFailedException
     */
    abstract public function doPostBack(HTTPRequest $request, Order $order);

    /**
     * Store the transaction id on the order so we dont lose the session
     * Only usefull when Order.place_before_payment is set to true
     */
    public function storeTransactionID(HTTPRequest $request, Order $order)
    {
    }
}
