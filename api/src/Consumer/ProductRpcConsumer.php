<?php

namespace App\Consumer;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Consumer for rpc server
 * Class ProductRpcConsumer
 * @package App\Consumer
 */
class ProductRpcConsumer  implements ConsumerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $bodyMsg = json_decode($msg->getBody(), true);
        $product = $this->entityManager->getRepository(Product::class)->find($bodyMsg['id']);

        #Close connection to avoid same return when multiple calls
        $this->entityManager->clear();
        $this->entityManager->getConnection()->close();
        return $this->serializer->serialize($product, 'json');
    }
}