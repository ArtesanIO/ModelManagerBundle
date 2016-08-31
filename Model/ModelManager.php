<?php

/*
 * This file is part of the ModelManagerBundle package.
 *
 * (c) Cristian Angulo <cristianangulonova@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArtesanIO\ModelManagerBundle\Model;

use ArtesanIO\ModelManagerBundle\Event\ModelEvent;
use ArtesanIO\ModelManagerBundle\Model\ModelManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ModelManager extends ContainerAware implements ModelManagerInterface
{
    protected $class;
    protected $em;
    protected $event_dispatcher;
    protected $form_factory;
    protected $type;

    public function __construct($class, $type = null)
    {
        $this->class = $class;
        $this->type = $type;
    }

    protected function get($id)
    {
        return $this->container->get($id);
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function getClass()
    {
        $metadata = $this->getEntityManager()->getClassMetadata($this->class);

        return $metadata->name;
    }

    public function getRepository()
    {
        return $this->getEntityManager()->getRepository($this->getClass());
    }

    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    public function findOneBy($array = array())
    {
        return $this->getRepository()->findOneBy($array);
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function entityPrefix()
    {
        $prefix = explode('\\', $this->getClass());

        return strtolower(end($prefix));
    }

    public function setDispatcher($event_dispatcher)
    {
        $this->event_dispatcher = $event_dispatcher;
    }

    public function getDispatcher()
    {
        return $this->event_dispatcher;
    }

    public function create()
    {
        $class = $this->getClass();

        return new $class;
    }

    public function save($model, $flush = true)
    {
        $this->getDispatcher()->dispatch($this->entityPrefix() . '.model_before_save.event', new ModelEvent($model, $this->container));

        $this->persist($model, $flush);

        $this->getDispatcher()->dispatch($this->entityPrefix() . '.model_after_save.event', new ModelEvent($model, $this->container));

        return $model;
    }

    protected function persist($model, $flush = true)
    {
        $this->getEntityManager()->persist($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete($model, $flush = true)
    {
        $this->getDispatcher()->dispatch($this->entityPrefix() . '.model_before_delete.event', new ModelEvent($model, $this->container));

        $this->remove($model, $flush);

        $this->getDispatcher()->dispatch($this->entityPrefix() . '.model_after_delete.event', new ModelEvent($model, $this->container));
    }

    protected function remove($model, $flush = true)
    {
        $this->getEntityManager()->remove($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function reload($model)
    {
        $this->getEntityManager->refresh($model);
    }

    public function isDebug()
    {
        return $this->get('kernel')->isDebug();
    }

    public function setFormFactory($form_factory)
    {
      $this->form_factory = $form_factory;
    }

    public function getFormFactory()
    {
      return $this->form_factory;
    }

    public function form($data = null, array $options = array())
    {
      if(!$this->type){
        throw new HttpException(404, "It's not defined an Entity Type");
      }

      return $this->getFormFactory()->create($this->type, $data, $options);
    }
}
