<?php

namespace ArtesanIO\ArtesanusBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ManagerEvent extends Event
{
    private $model;
    private $container;

    function __construct($model, $container)
    {
        $this->model = $model;
        $this->container = $container;
    }

    public function getModel()
    {
        return $this->model;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
}
