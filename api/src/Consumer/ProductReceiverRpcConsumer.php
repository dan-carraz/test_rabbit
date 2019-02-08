<?php

namespace App\Consumer;


use App\Entity\ProductReceiverRpc;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Consumer that check if product exists and get it back with rpc
 * Class ProductReceiverConsumer
 * @package App\Consumer
 */
class ProductReceiverRpcConsumer extends AbstractProductReceiverConsumer
{
    /**
     * @var RpcClient
     */
    private $rpcClient;

    /**
     * ProductReceiverRpcConsumer constructor.
     * @param RpcClient $rpcClient
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(RpcClient $rpcClient, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        parent::__construct($entityManager, $serializer);
        $this->rpcClient = $rpcClient;
    }

    /**
     * Returns json data of product
     *
     * @param array $bodyMsg
     * @return string
     */
    protected function request(array $bodyMsg): string
    {
        //Call RPC server to get Product data
        $requestId = uniqid();
        $this->rpcClient->addRequest(json_encode($bodyMsg), 'product_exchange', $requestId, 'product.rpc');
        $replies = $this->rpcClient->getReplies();

        $productData = $replies[$requestId];

        #reset the rpc client at the end
        $this->rpcClient->reset();

        return $productData;
    }

    /**
     * Get product receiverNamespace
     *
     * @return string
     */
    protected function getProductReceiverNamespace(): string
    {
        return ProductReceiverRpc::class;
    }
}