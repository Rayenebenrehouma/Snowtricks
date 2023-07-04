<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'videoId', cascade: ["remove"])]
    #[ORM\JoinColumn(name: 'link_id', referencedColumnName: 'id', nullable: false, onDelete: "CASCADE")]
    private ?Figure $link = null;



    #[ORM\Column(length: 255)]
    private ?string $video_name = null;

    #[ORM\Column]
    private ?bool $is_deleted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?figure
    {
        return $this->link;
    }

    public function setLink(?figure $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getVideoName(): ?string
    {
        return $this->video_name;
    }

    public function setVideoName(string $video_name): self
    {
        $this->video_name = $video_name;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }
}
