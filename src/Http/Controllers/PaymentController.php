<?php

namespace Webkul\AzamPay\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\AzamPay\Helpers\AzamPayHelper;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     *
     * @var OrderRepository
     * @var InvoiceRepository
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
    ) {
        //
    }

    /**
     * Redirects to the AzamPay payment gateway.
     */
    public function redirect(): RedirectResponse
    {
        $cart = Cart::getCart();
        $billingAddress = $cart->billing_address;

        $paymentData = [
            'amount'          => $cart->grand_total, // Amount to charge
            'currency'        => $cart->global_currency_code,
            'description'     => 'AzamPay Checkout Payment for order id - '.$cart->id,
            'client_id'       => core()->getConfigData('sales.payment_methods.azampay.client_id'),
            'client_secret'   => core()->getConfigData('sales.payment_methods.azampay.client_secret'),
            'success_url'     => route('azampay.success'),
            'cancel_url'      => route('azampay.cancel'),
        ];

        $azampayHelper = new AzamPayHelper(); // Assuming this helper manages the AzamPay integration
        $checkoutSessionUrl = $azampayHelper->createCheckoutSession($paymentData);

        return redirect()->away($checkoutSessionUrl);
    }

    /**
     * Place an order and redirect to the success page after successful AzamPay payment.
     */
    public function success(): RedirectResponse
    {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Prepares order's invoice data for creation.
     */
    protected function prepareInvoiceData($order): array
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice'  => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
