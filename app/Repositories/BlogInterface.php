<?php
namespace App\Repositories;

interface BlogInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function create($data);
    public function getByID($id);
    public function updateByID($id,$data);
    public function updateAll($data);
    public function destroyByIDs($id);
    public function getListPaginate($perPage = 20, $filter = []);
    public function getAll($filter = []);
    public function getBySlug($slug);
    public function checkSlug($slug, $id = 0);
}
