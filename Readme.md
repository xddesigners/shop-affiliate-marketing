# XD Silvershop Affiliate marketing

Adds affiliate marketing s2s postback support to SilverShop orders. The module comes with a abstract AffiliateProvider class where implementation for different providers can be based upon. A default implementation for Tune's HasOffers platform already exists.

## What it is

Affiliate marketing is used by marketeers to generate leads with affiliate networks. Affilate partners can use links with affliate id's in blog posts for example.

## Configuration

### Has Offers

You can configure the following parameters on the Has Offers implementation:

```yml
XD\Shop\AffiliateMarketing\Providers\HasOffers:
  default_offer_id: 71 # the default offer to map your conversions to
  transaction_id_var: 'clickid' # the query parameter name that holds the transaction id
  affiliate_id_var: 'pub' # the query parameter name that holds the affiliate id
```

If you work with multiple goals or offers id's and don't want to make use of the default parameter you can extend the `HasOffers` class:

```php
class HasOffersExtension extends Extension
{
  public function onBeforePostBack(&$query, $order)
  {
    // if the order matches the case of the different offer or goal
    if ($order->matchesDifferentCase()) {
      $query['offer_id'] = 'my_offer_id';
    }
  }
}
```

### Create a custom AffiliateProvider

If you have a different affiliate provider you can extend the abstract AffiliateProvider class and implement the methods `sessionFromRequest` and `doPostBack`. The first method is used to get and store the id you need for your postback in the session. The `doPostBack` method is called by the order when paid and should call the provider's server with the stored id.

```php
class MyAffiliateProvider extends AffiliateProvider
{ 
    /**
     * Set the required session data from the request
     */
    public function sessionFromRequest(HTTPRequest $request)
    {
        $transactionId = $request->getVar('transaction_id');
        $affiliateId = $request->getVar('affiliate_id');
        if ($transactionId && $affiliateId) {
            $session = $request->getSession();
            $session->set('MyAffiliateProvider.TransactionID', $transactionId);
            $session->set('MyAffiliateProvider.AffiliateID', $affiliateId);
            $session->save($request);
        }
    }

    /**
     * Handle the postback to the affiliate provider
     */
    public function doPostBack(HTTPRequest $request, Order $order)
    {
        // send the postback to your affiliate partners server
    }
}
```

Configure the injector to use your custom affiliate provider:

```yml
SilverStripe\Core\Injector\Injector:
  AffiliateProvider:
    class: MyAffiliateProvider
```
