<?php

namespace OroB2B\Bundle\OrderBundle\EventListener\Order;

use OroB2B\Bundle\OrderBundle\Event\OrderEvent;
use OroB2B\Bundle\PaymentBundle\Provider\PaymentTermProvider;

class OrderPaymentTermEventListener
{
    const ACCOUNT_PAYMENT_TERM_KEY = 'accountPaymentTerm';
    const ACCOUNT_GROUP_PAYMENT_TERM_KEY = 'accountGroupPaymentTerm';

    /** @var PaymentTermProvider */
    protected $provider;

    /**
     * @param PaymentTermProvider $provider
     */
    public function __construct(PaymentTermProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param OrderEvent $event
     */
    public function onOrderEvent(OrderEvent $event)
    {
        $accountPaymentTerm = null;
        $accountGroupPaymentTerm = null;
        $order = $event->getOrder();
        $account = $order->getAccount();

        if ($account) {
            $paymentTerm = $this->provider->getAccountPaymentTerm($account);
            $accountPaymentTerm = $paymentTerm ? $paymentTerm->getId() : null;
        }

        if ($account && $account->getGroup()) {
            $paymentTerm = $this->provider->getAccountGroupPaymentTerm($account->getGroup());
            $accountGroupPaymentTerm = $paymentTerm ? $paymentTerm->getId() : null;
        }

        $event->getData()->offsetSet(self::ACCOUNT_PAYMENT_TERM_KEY, $accountPaymentTerm);
        $event->getData()->offsetSet(self::ACCOUNT_GROUP_PAYMENT_TERM_KEY, $accountGroupPaymentTerm);
    }
}
