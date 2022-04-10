<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string', length: 128)]
    private string $id;

    #[ORM\Column(name: 'name', type: 'string', length: 64)]
    private ?string $name;

    #[ORM\Column(name: 'secret', type: 'string', length: 255)]
    private string $secret;

    /**
     * @throws Exception
     */
    public function __construct(?string $name = null)
    {
        $this->id = bin2hex(random_bytes(16));
        $this->secret = bin2hex(random_bytes(32));
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

}
