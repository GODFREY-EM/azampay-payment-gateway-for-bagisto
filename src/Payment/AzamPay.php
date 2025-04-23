<?php

namespace Webkul\AzamPay\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Checkout\Facades\Cart;
use Webkul\Payment\Payment\Payment;

class AzamPay extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'azampay';

    /**
     * Returns the redirect URL for AzamPay.
     */
    public function getRedirectUrl(): string
    {
        return route('azampay.redirect');
    }

    /**
     * Returns AzamPay payment method image.
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/azampay.png', 'shop');
    }

    /**
     * Retrieves the configuration data for AzamPay.
     */
    public function getConfigData(string $key)
    {
        return core()->getConfigData('sales.payment_methods.' . $this->code . '.' . $key);
    }
}
