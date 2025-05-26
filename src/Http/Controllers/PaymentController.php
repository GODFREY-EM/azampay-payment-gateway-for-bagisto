<?php

namespace Webkul\AzamPay\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\AzamPay\Helpers\AzamPayHelper;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
    ) {
        //
    }

    /**
     * Redirects the customer to the AzamPay payment gateway.
     */
    public function redirect(): RedirectResponse
    {
        $cart = Cart::getCart();

        // Prepare the cart items for AzamPay
        $cartItems = [];
        foreach ($cart->items as $item) {
            $cartItems[] = [
                'name'     => $item->name,
                'sku'      => $item->sku,
                'price'    => $item->price,
                'quantity' => $item->quantity,
                // Add more fields as expected by AzamPay if needed
            ];
        }

        $paymentData = [
            'amount'         => $cart->grand_total,
            'currency'       => $cart->global_currency_code,
            'order_id'       => (string) $cart->id,
            'success_url'    => route('azampay.success'),
            'fail_url'       => route('azampay.cancel'),
            'cancel_url'     => route('azampay.cancel'),
            'vendor_id'      => env('AZAMPAY_VENDOR_ID', config('azampay.vendor_id')),
            'vendor_name'    => env('AZAMPAY_VENDOR_NAME', config('azampay.vendor_name')),
            'request_origin' => config('app.url'),
            'language'       => 'en',
            'cart'           => ['items' => $cartItems],
        ];

        $azamPayHelper = new AzamPayHelper();

        try {
            $checkoutSessionUrl = $azamPayHelper->createCheckoutSession($paymentData);
            return redirect()->away($checkoutSessionUrl);
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'AzamPay redirection failed: ' . $e->getMessage());
            return redirect()->route('shop.checkout.onepage.index');
        }
    }

    /**
     * Handles the AzamPay success redirect and finalizes the order.
     */
    public function success(): RedirectResponse
    {
        $cart = Cart::getCart();
        $orderData = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($orderData);
        $order = $this->orderRepository->find($order->id);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();
        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Handles the AzamPay cancel/failure redirect.
     */
    public function cancel(): RedirectResponse
    {
        session()->flash('error', 'Your AzamPay payment was cancelled or failed.');
        return redirect()->route('shop.checkout.onepage.index');
    }

    /**
     * Prepares invoice data from the order for creation.
     */
    protected function prepareInvoiceData($order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[$item->id] = $item->qty_to_invoice;
        }

        return [
            'order_id' => $order->id,
            'invoice'  => ['items' => $items],
        ];
    }
}
