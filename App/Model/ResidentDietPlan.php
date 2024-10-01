<?php

namespace App\Model;

class ResidentDietPlan
{
    private ?int $id;
    private int $foodId;
    private int $residentId;
    private string $createdBy;
    private ?string $createdOn;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->foodId = $data['food_id'] ?? 0;
        $this->residentId = $data['resident_id'] ?? 0;
        $this->createdBy = $data['created_by'] ?? '';
        $this->createdOn = $data['created_on'] ?? null; 
    }

    // Getter and Setter for ID
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // Getter and Setter for Food ID
    public function getFoodId(): int
    {
        return $this->foodId;
    }

    public function setFoodId(int $foodId): void
    {
        $this->foodId = $foodId;
    }

    // Getter and Setter for Resident ID
    public function getResidentId(): int
    {
        return $this->residentId;
    }

    public function setResidentId(int $residentId): void
    {
        $this->residentId = $residentId;
    }

    // Getter and Setter for Created By
    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    // Getter and Setter for Created On
    public function getCreatedOn(): ?string
    {
        return $this->createdOn;
    }

    public function setCreatedOn(string $createdOn): void
    {
        $this->createdOn = $createdOn;
    }

    public function jsonSerialize(): array
    {
        return [ // Converting ResidentDietPlan type to Array
            'id' => $this->id,
            'food_id' => $this->foodId,
            'resident_id' => $this->residentId,
            'created_by' => $this->createdBy,
            'created_on' => $this->createdOn, // Include created_on if needed
        ];
    }
}