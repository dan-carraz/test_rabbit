<?php

namespace App\Consumer;


use App\Entity\ProductReceiver;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ProductReceiverConsumer implements ConsumerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $bodyMsg = json_decode($msg->getBody(), true);

        $productReceiver = $this->entityManager->getRepository(ProductReceiver::class)->find($bodyMsg['id']);

        if (! $productReceiver instanceof ProductReceiver) {
            $productReceiver = new ProductReceiver();
        }
    }
}