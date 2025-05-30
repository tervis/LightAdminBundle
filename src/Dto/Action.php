<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\Dto;

class Action
{
    private array $fields = [];

    private ?string $path = null;

    private array $options = [];

    public static function new(string $path, array $fields = [])
    {
        return (new self())
            ->setFields($fields)
            ->setPath($path)
        ;
    }

    /**
     * Get the value of fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the value of fields
     *
     * @return  self
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the value of path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value of options
     *
     * @return  self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}
