<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use App\Service\TokenIdGenerator;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: TokenIdGenerator::class)]
    #[ORM\Column(type: 'string', length: 128)]
    private ?string $id = null;

    #[ORM\Column(name: 'expires_at', type:'datetime')]
    private DateTimeInterface $expires;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'app_user', referencedColumnName: 'id')]
    private User $user;

    public function __construct(User $user, ?DateTimeInterface $expires = null)
    {
        $this->user = $user;
        $this->expires = $expires ?? new \DateTime('90 days');
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expires;
    }

    public function isExpired(): bool
    {
        return (new \DateTime('now'))->getTimestamp() < $this->expires->getTimestamp();
    }
}
