<?php
namespace App\Repositories;

interface BannerInterface {
    /**
     * Get list by type
     * @param interger $type
     * @return mixed
     */
    public function getListByType($type);
    /**
     * Get all approved banner with paginate
     * @param interger $perPage
     * @return mixed
     */
    public function getAllListBannerPaginate($perPage = 20,$filter);

    /**
     * Create a new banner
     * @return mixed
     */
    public function createNewBanner($data);

    /**
     * Get a  banner detail
     * @return mixed
     */
    public function getBannerByID($id);

    /**
     * Update a  banner
     * @return mixed
     */
    public function updateBannerByID($id,$data);

    /**
     * Delete a  banner
     * @return mixed
     */
    public function destroyBannerByIDs($id);

}
