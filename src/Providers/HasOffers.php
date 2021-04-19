<?php

namespace XD\Shop\AffiliateMarketing\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SilverShop\Model\Order;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use XD\Shop\AffiliateMarketing\Exceptions\PostbackFailedException;

class HasOffers extends AffiliateProvider
{
    use Configurable;
    use Extensible;

    const TRANSACTION_SESSION_KEY = 'AffiliateProvider.HasOffers.TransactionId';
    const AFFILIATE_SESSION_KEY = 'AffiliateProvider.HasOffers.AffiliateId';

    private static $transaction_id_var = 'clickid';

    private static $affiliate_id_var = 'pub';

    private static $default_offer_id;
    
    /**
     * Set the required session data from the request
     */
    public function sessionFromRequest(HTTPRequest $request)
    {
        $transactionId = $request->getVar(self::config()->get('transaction_id_var'));
        $affiliateId = $request->getVar(self::config()->get('affiliate_id_var'));
        if ($transactionId || $affiliateId) {
            $session = $request->getSession();
            $session->set(self::TRANSACTION_SESSION_KEY, $transactionId);
            $session->set(self::AFFILIATE_SESSION_KEY, $affiliateId);
            $session->save($request);
        }
    }

    /**
     * Handle the postback to the affiliate provider
     */
    public function doPostBack(HTTPRequest $request, Order $order)
    {
        if (!$request || !($session = $request->getSession())) {
            return;
        }

        if ($transactionId = $session->get(self::TRANSACTION_SESSION_KEY)) {
            $query = [
                'offer_id' => self::config()->get('default_offer_id'),
                'transaction_id' => $transactionId,
                'adv_sub' => $order->MemberID
            ];

            $this->extend('onBeforePostBack', $query, $order);
            $query = array_filter($query);

            try {
                $client = new Client();
                $res = $client->request('GET', 'https://finch.go2cloud.org/aff_lsr', ['query' => $query]);
                if ($res->getStatusCode() !== 200) {
                    throw new PostbackFailedException('Failed to call postback', $res->getStatusCode());
                }
            } catch (GuzzleException $e) {
                throw new PostbackFailedException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
}
