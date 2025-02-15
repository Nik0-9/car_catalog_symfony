<?php

namespace App\Entity;

use App\Enum\CarStatus;
use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Please enter a brand')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Brand must be at least {{ limit }} characters",
        maxMessage: "Brand can't exceed {{ limit }} characters"
    )]
    private ?string $brand = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Please enter a model")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Model must be at least {{ limit }} characters",
        maxMessage: "Model can't exceed {{ limit }} characters"
    )]
    private ?string $model = null;
    
    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message: "Please enter a production year")]
    private ?int $production_year = null;
    
    /**
     * this function checks if the production year is between 1900 and the current year
     */
    #[Assert\Callback]
    public function validateProductionYear(ExecutionContextInterface $context):void{
        $currentYear = (int)date('Y');
        if($this->production_year > $currentYear || $this->production_year < 1900){
            $context->buildViolation('Production year must be between 1900 and the current year')
                ->atPath('production_year')
                ->addViolation();
        }
    }

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Please enter a price")]
    #[Assert\Positive(message: "Price must be higher than 0")]
    #[Assert\LessThan(9999999.99, message: "Price must be lower than 99.999.999,99")]
    private ?string $price = null;

    #[ORM\Column(enumType: CarStatus::class)]
    private ?CarStatus $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getProductionYear(): ?int
    {
        return $this->production_year;
    }

    public function setProductionYear(int $production_year): static
    {
        $this->production_year = $production_year;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?CarStatus
    {
        return $this->status;
    }

    public function setStatus(CarStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }
}
