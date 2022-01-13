<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $author;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $chat;

    #[ORM\Column(type: 'string', length: 255)]
    private $photoId;

    #[ORM\Column(type: 'text', nullable: true)]
    private $response;

    #[ORM\Column(type: 'text')]
    private $rawData;

    #[ORM\Column(type: 'datetime')]
    private $handled;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $responsed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getChat(): ?string
    {
        return $this->chat;
    }

    public function setChat(?string $chat): self
    {
        $this->chat = $chat;

        return $this;
    }

    public function getPhotoId(): ?string
    {
        return $this->photoId;
    }

    public function setPhotoId(string $photoId): self
    {
        $this->photoId = $photoId;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getRawData(): ?string
    {
        return $this->rawData;
    }

    public function setRawData(string $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getHandled(): ?\DateTimeInterface
    {
        return $this->handled;
    }

    public function setHandled(\DateTimeInterface $handled): self
    {
        $this->handled = $handled;

        return $this;
    }

    public function getResponsed(): ?\DateTimeInterface
    {
        return $this->responsed;
    }

    public function setResponsed(?\DateTimeInterface $responsed): self
    {
        $this->responsed = $responsed;

        return $this;
    }
}
