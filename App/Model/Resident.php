<?php

namespace App\Model;

class Resident
{
    private ?int $id;
    private string $name;
    private ?string $iddsi_level;
    private string $created_by;
    private ?string $created_on;
    private ?string $iddsiLevelName;

    public function __construct(array $resident)
    {
        // Assigning fields 
        $this->id = isset($resident['id']) ? (int)$resident['id'] : null;
        $this->name = $resident['name'] ?? '';
        $this->iddsi_level = $resident['iddsi_level'] ?? null;
        $this->created_by = $resident['created_by'] ?? '';
        $this->created_on = $resident['created_on'] ?? null;
        $this->iddsiLevelName = $resident['iddsiLevelName'] ?? null;
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

    // Getter and Setter for Resident Name
    public function getResidentName(): string
    {
        return $this->name;
    }

    public function setResidentName(string $residentName): void
    {
        $this->name = $residentName;
    }

    // Getter and Setter for IDDSI Level
    public function getIddsiLevel(): ?string
    {
        return $this->iddsi_level;
    }

    public function setIddsiLevel(?string $iddsiLevel): void
    {
        $this->iddsi_level = $iddsiLevel;
    }

    // Getter and Setter for Created By
    public function getCreatedBy(): string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $createdBy): void
    {
        $this->created_by = $createdBy;
    }

    // Getter and Setter for Created On
    public function getCreatedOn(): ?string
    {
        return $this->created_on;
    }

    public function setCreatedOn(?string $createdOn): void
    {
        $this->created_on = $createdOn;
    }

    // Getter and Setter for IDDSI Level Name
    public function getIddsiLevelName(): ?string
    {
        return $this->iddsiLevelName;
    }

    public function setIddsiLevelName(?string $iddsiLevelName): void
    {
        $this->iddsiLevelName = $iddsiLevelName;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'iddsi_level' => $this->iddsi_level,
            'iddsiLevelName' => $this->iddsiLevelName,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
        ];
    }
}