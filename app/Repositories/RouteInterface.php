<?php
namespace App\Repositories;

interface RouteInterface {
    public function getAll($filter = []);
    public function create($data);
    public function getByID($id);
    public function getBySlug($slug);
    public function getByTypeID($type, $type_id);
    public function checkSlug($slug, $type = 0, $type_id = 0);
    public function updateByID($id, $data);
    public function updateByTypeID($type, $type_id, $data);
    public function destroyByIDs($id);
    public function destroyByTypeIDs($type, $type_ids);
}
