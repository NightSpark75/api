<?php 
namespace App\Repositories\Criteria;

use App\Interfaces\RepositoryInterface as Repository;
use App\Interfaces\RepositoryInterface;

abstract class Criteria {

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public abstract function apply($model, Repository $repository);
}