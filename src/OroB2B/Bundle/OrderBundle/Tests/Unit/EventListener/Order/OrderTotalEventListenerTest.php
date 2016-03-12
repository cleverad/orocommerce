<?php

namespace OroB2B\Bundle\OrderBundle\Tests\Unit\EventListener\Order;

use Symfony\Component\Form\FormInterface;

use OroB2B\Bundle\OrderBundle\Entity\Order;
use OroB2B\Bundle\OrderBundle\Event\OrderEvent;
use OroB2B\Bundle\OrderBundle\SubtotalProcessor\TotalProcessorProvider;
use OroB2B\Bundle\OrderBundle\EventListener\Order\OrderTotalEventListener;

class OrderTotalEventListenerTest extends \PHPUnit_Framework_TestCase
{
    use SubtotalTrait;

    /** @var OrderTotalEventListener */
    protected $listener;

    /** @var TotalProcessorProvider|\PHPUnit_Framework_MockObject_MockObject */
    protected $totalProcessorProvider;

    protected function setUp()
    {
        $this->totalProcessorProvider = $this
            ->getMockBuilder('OroB2B\Bundle\OrderBundle\SubtotalProcessor\TotalProcessorProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new OrderTotalEventListener($this->totalProcessorProvider);
    }

    protected function tearDown()
    {
        unset($this->listener, $this->totalProcessorProvider);
    }

    public function testOnOrderEvent()
    {
        /** @var FormInterface|\PHPUnit_Framework_MockObject_MockObject $form */
        $form = $this->getMock('Symfony\Component\Form\FormInterface');

        $order = new Order();

        $total = $this->getSubtotal('type', 'label', 100, 'USD', true);

        $this->totalProcessorProvider->expects($this->once())
            ->method('getTotal')
            ->with($order)
            ->willReturn($total);

        $event = new OrderEvent($form, $order);
        $this->listener->onOrderEvent($event);

        $actualData = $event->getData()->getArrayCopy();

        $this->assertArrayHasKey(OrderTotalEventListener::TOTAL_KEY, $actualData);
        $this->assertEquals($total->toArray(), $actualData[OrderTotalEventListener::TOTAL_KEY]);
    }
}
