<?php
namespace App\Services;

use App\Repositories\RouteInterface;

class RouteService extends BaseService {
    protected $route;
    function __construct(
        RouteInterface $route
    ){
        $this->route = $route;
    }

    public function getBySlug($slug){
        return $this->route->getBySlug($slug);
    }

    public function getAll($filter = []){
        return $this->route->getAll($filter);
    }
}
