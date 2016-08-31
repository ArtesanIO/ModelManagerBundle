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

interface ModelManagerInterface
{
    public function delete($model, $flush = true);

    public function findAll();

    public function find($id);

    public function findOneBy($array = array());

    public function getClass();

    public function getDispatcher();

    public function getRepository();

    public function isDebug();

    public function reload($model);

    public function save($model, $flush= true);
}
