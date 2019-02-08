<?php

namespace App\Consumer;


use App\Entity\ProductReceiverGuzzle;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Consumer that check if product exists and get it back with guzzle
 * Class ProductReceiverConsumer
 * @package App\Consumer
 */
class ProductReceiverGuzzleConsumer extends AbstractProductReceiverConsumer
{
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * ProductReceiverGuzzleConsumer constructor.
     * @param ClientInterface $guzzleClient
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(ClientInterface $guzzleClient, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        parent::__construct($entityManager, $serializer);
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * Returns json data of product
     *
     * @param array $bodyMsg
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(array $bodyMsg): string
    {
        //Call Guzzle request
        $response = $this->guzzleClient->request('GET', sprintf('/products/%s', $bodyMsg['id']));

        return $response->getBody()->getContents();
    }

    /**
     * Get product receiverNamespace
     *
     * @return string
     */
    protected function getProductReceiverNamespace(): string
    {
        return ProductReceiverGuzzle::class;
    }
}