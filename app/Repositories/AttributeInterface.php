<?php
namespace App\Repositories;

interface AttributeInterface {
    public function getAll();
    public function create($data);
    public function getByID($id);
    public function updateByID($id, $data);
    public function destroyByIDs($id);
    public function getByEntity($entity);
    public function getByEntityAndEntityID($entity, $entity_id);
}
