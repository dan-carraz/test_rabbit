<?php

namespace App\Consumer;


use App\Entity\ProductReceiver;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Consumer that check if product exists and get it back with rpc
 * Class ProductReceiverConsumer
 * @package App\Consumer
 */
class ProductReceiverConsumer implements ConsumerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RpcClient
     */
    private $rpcClient;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(RpcClient $rpcClient, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->rpcClient = $rpcClient;
        $this->serializer = $serializer;
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $bodyMsg = json_decode($msg->getBody(), true);

        $productReceiver = $this->entityManager->getRepository(ProductReceiver::class)->find($bodyMsg['id']);

        //Create new product
        if (! $productReceiver instanceof ProductReceiver) {
            $productReceiver = new ProductReceiver();
        }

        //Call RPC server to get Product data
        $requestId = uniqid();
        $this->rpcClient->addRequest($msg->getBody(), 'product_exchange', $requestId, 'product.rpc');
        $replies = $this->rpcClient->getReplies();

        $productData = $replies[$requestId];
        $productReceiver = $this->serializer->deserialize(
            $productData,
            ProductReceiver::class,
            'json',
            ['object_to_populate' => $productReceiver]
        );

        $this->entityManager->persist($productReceiver);
        $this->entityManager->flush();

        #reset the rpc client at the end
        $this->rpcClient->reset();

        return ConsumerInterface::MSG_ACK;
    }
}