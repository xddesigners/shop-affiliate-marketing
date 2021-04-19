<?php

namespace XD\Shop\AffiliateMarketing\Extensions;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use XD\Shop\AffiliateMarketing\Providers\AffiliateProvider;

/**
 * class ControllerExtension
 * @property Controller $owner
 */
class ControllerExtension extends Extension
{
    public function onAfterInit()
    {
        if ($request = $this->owner->getRequest()) {
            /** @var AffiliateProvider $provider */
            $provider = Injector::inst()->create('AffiliateProvider');
            $provider->sessionFromRequest($request);
        }
    }
}
