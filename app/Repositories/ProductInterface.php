<?php
namespace App\Repositories;

interface ProductInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function create($data);
    public function getByID($id);
    public function updateByID($id,$data);
    public function destroyByIDs($id);
    public function getActiveListPaginate($perPage = 20, $filter = []);
    public function getAllActive($filter = []);
    public function getBySlug($slug);
    public function checkSlug($slug, $id = 0);
}
