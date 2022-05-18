<?php

namespace App\Entity;

use App\Enum\Status;
use App\Repository\RequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'json')]
    private $request_data = [];

    #[ORM\Column(type: 'boolean')]
    private $useProxy;

    #[ORM\Column(type: 'boolean')]
    private $saveResponse;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $executionsWithError;

    #[ORM\Column(type: 'datetime', options: ['default' => "CURRENT_TIMESTAMP"])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', options: ['default' => "CURRENT_TIMESTAMP"])]
    private $nextAttemptAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $finishedAt;

    #[ORM\Column(type: 'integer', options: ['default' => Status::PENDING])]
    private $status;

    #[ORM\OneToMany(mappedBy: 'request', targetEntity: Response::class)]
    private $responses;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestData(): ?array
    {
        return $this->request_data;
    }

    public function setRequestData(array $request_data): self
    {
        $this->request_data = $request_data;

        return $this;
    }

    public function isUseProxy(): ?bool
    {
        return $this->useProxy;
    }

    public function setUseProxy(bool $useProxy): self
    {
        $this->useProxy = $useProxy;

        return $this;
    }

    public function isSaveResponse(): ?bool
    {
        return $this->saveResponse;
    }

    public function setSaveResponse(bool $saveResponse): self
    {
        $this->saveResponse = $saveResponse;

        return $this;
    }

    public function getExecutionsWithError(): ?int
    {
        return $this->executionsWithError;
    }

    public function setExecutionsWithError(int $executionsWithError): self
    {
        $this->executionsWithError = $executionsWithError;

        return $this;
    }

    public function incrementsError(): self
    {
        $this->executionsWithError++;
        return $this;

    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getNextAttemptAt(): ?\DateTime
    {
        return $this->nextAttemptAt;
    }

    public function setNextAttemptAt(?\DateTime $nextAttemptAt): self
    {
        $this->nextAttemptAt = $nextAttemptAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTime
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTime $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status->value;

        return $this;
    }

    /**
     * @return Collection<int, Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
            $response->setRequest($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): self
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getRequest() === $this) {
                $response->setRequest(null);
            }
        }

        return $this;
    }
}
