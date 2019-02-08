<?php

namespace App\Consumer;

use App\Entity\AbstractProduct;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractProductReceiverConsumer implements ConsumerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Returns json data of product
     *
     * @param array $bodyMsg
     * @return string
     */
    protected abstract function request(array $bodyMsg) : string;

    /**
     * Get product receiverNamespace
     *
     * @return string
     */
    protected abstract function getProductReceiverNamespace() : string;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }


    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     * @throws \ReflectionException
     */
    public function execute(AMQPMessage $msg)
    {
        $time_start = microtime(true);
        $bodyMsg = json_decode($msg->getBody(), true);

        $productReceiver = $this->entityManager->getRepository($this->getProductReceiverNamespace())->find($bodyMsg['id']);

        //Create new product
        if (! $productReceiver instanceof AbstractProduct) {
            $reflexion = new \ReflectionClass($this->getProductReceiverNamespace());
            $productReceiver = $reflexion->newInstanceArgs();
        }

        $productData = $this->request($bodyMsg);

        $productReceiver = $this->serializer->deserialize(
            $productData,
            $this->getProductReceiverNamespace(),
            'json',
            ['object_to_populate' => $productReceiver]
        );

        $this->entityManager->persist($productReceiver);
        $this->entityManager->flush();

        $time_end = microtime(true);

        var_dump( ($time_end - $time_start));

        return ConsumerInterface::MSG_ACK;
    }
}