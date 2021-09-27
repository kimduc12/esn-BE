<?php
namespace App\Repositories;

interface TopicInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function create($data);
    public function getByID($id);
    public function updateByID($id,$data);
    public function update($data);
    public function destroyByIDs($id);
    public function getListPaginate($perPage = 20, $filter = []);
    public function getAll($filter = []);
    public function getOneActive();
}
