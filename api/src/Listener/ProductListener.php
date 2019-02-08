<?php

namespace App\Listener;

use App\Entity\Product;
use Doctrine\ORM\Event\LifecycleEventArgs;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class ProductListener
{
    /**
     * @var ProducerInterface
     */
    private $productProducer;

    /**
     * @var array
     */
    private $eventsToSendOnPostFlush;

    public function __construct(ProducerInterface $productProducer)
    {

        $this->productProducer = $productProducer;
        $this->eventsToSendOnPostFlush = [];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs) : void
    {
        $this->sendMessage($eventArgs);
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate (LifecycleEventArgs $eventArgs) : void
    {
        $this->eventsToSendOnPostFlush[] = $eventArgs;
    }

    /**
     * PostFlush event mandatory for rpc to avoid a call before data is commited
     */
    public function postFlush()
    {
        foreach($this->eventsToSendOnPostFlush as $event) {
            $this->sendMessage($event);
        }
        $this->eventsToSendOnPostFlush = [];
    }

    private function sendMessage(LifecycleEventArgs $eventArgs) : void
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof Product) {
            return;
        }

        $message = json_encode(['id' => $entity->getId()]);

        $this->productProducer->publish($message, 'product_with_rpc');
        $this->productProducer->publish($message, 'product_with_guzzle');
    }
}