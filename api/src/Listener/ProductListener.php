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
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof Product) {
            return;
        }

        $message = ['id' => $entity->getId()];

        $this->productProducer->publish(json_encode($message), 'product');
    }
}