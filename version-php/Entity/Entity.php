<?php

class Entity
{
    protected array $data;

    public function __construct(array $entityData)
    {
        $this->data = $entityData;
    }

    protected function getId(): int{
        return $this->data["id"];
    }
}