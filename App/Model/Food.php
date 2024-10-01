<?php
namespace App\Model;

class Food
{
    private $id;
    private $name;
    private $category;
    private $iddsi_level;
    private $created_by;
    private $created_on;
    private $categoryName;
    private $iddsiLevelName;

    public function __construct(array $data)
    {
        // Assigning fields 
        $this->id = $data['id'] ?? null; 
        $this->name = $data['name'] ?? ''; 
        $this->category = $data['category'] ?? ''; 
        $this->iddsi_level = $data['iddsi_level'] ?? null;
        $this->created_by = $data['created_by'] ?? ''; 
        $this->created_on = $data['created_on'] ?? null; 
        $this->categoryName = $data['categoryName'] ?? null; 
        $this->iddsiLevelName = $data['iddsiLevelName'] ?? null; 
    }

    // Getter and Setter for ID
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    // Getter and Setter for Food Name
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    // Getter and Setter for Category
    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    // Getter and Setter for IDDSI Level
    public function getIddsiLevel()
    {
        return $this->iddsi_level;
    }

    public function setIddsiLevel($iddsiLevel)
    {
        $this->iddsi_level = $iddsiLevel;
    }

    // Getter and Setter for Created By
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    public function setCreatedBy($createdBy)
    {
        $this->created_by = $createdBy;
    }

    // Getter and Setter for Created On
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    public function setCreatedOn($createdOn)
    {
        $this->created_on = $createdOn;
    }

    // Getter and Setter for IDDSI Level Name
    public function getIddsiLevelName()
    {
        return $this->iddsiLevelName;
    }

    public function setIddsiLevelName($iddsiLevelName)
    {
        $this->iddsiLevelName = $iddsiLevelName;
    }

    // Getter and Setter for Category Name
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    public function jsonSerialize()
    {
        // Converting Food to array
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'categoryName' => $this->categoryName,
            'iddsi_level' => $this->iddsi_level,
            'iddsiLevelName' => $this->iddsiLevelName,
            'created_by' => $this->created_by
        ];
    }
}
?>
