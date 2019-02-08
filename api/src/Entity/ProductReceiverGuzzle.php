<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 * @ORM\Entity
 */
class ProductReceiverGuzzle extends AbstractProduct
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    protected $id;

    /**
     * @param string $id
     * @return AbstractProduct
     */
    public function setId(string $id): AbstractProduct
    {
        $this->id = $id;
        return $this;
    }
}