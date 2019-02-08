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

    public function __construct(ProducerInterface $productProducer)
    {

        $this->productProducer = $productProducer;
    }

    public function postPersist(LifecycleEventArgs $eventArgs) : void
    {
        $this->sendMessage($eventArgs);
    }

    public function postUpdate (LifecycleEventArgs $eventArgs) : void
    {
        $this->sendMessage($eventArgs);
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