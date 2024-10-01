<?php
namespace App\Model;

class Category
{
	private $id;
    private $name;

    public function __construct($data)
    {
        //assigning fields
        if(isset($data['id']))
            $this->id = $data['id'] ? $data['id'] : "";
        $this->name = $data['name']? $data['name'] : "";
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

    public function setname($name)
    {
        $this->name = $name;
    }

    public function JsonSerializable () {
        //Converting Category to array
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
?>