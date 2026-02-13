<?php


class FormService
{
    protected array $data;

    public function __construct(array $postData)
    {
        $this->data = $postData;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        if (!isset($this->data[$key]) || $this->data[$key] === '') {
            return $default;
        }
        
        return htmlspecialchars(trim($this->data[$key]));
    }

    protected function inputInt(string $key, int $default = 0): int
    {
        return (int) $this->input($key, $default);
    }
}