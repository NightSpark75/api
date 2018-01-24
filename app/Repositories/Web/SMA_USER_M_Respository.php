<?php namespace App\Repositories\Web;
 
use App\Interfaces\RepositoryInterface;
use App\Repositories\Repository;
 
class ActorRepository extends Repository {
 
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Web\User';
    }
}