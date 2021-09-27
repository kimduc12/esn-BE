<?php
namespace App\Services;

use App\Constants\GiftConst;
use App\Constants\GiftExchangeConst;
use App\Repositories\GiftInterface;
use App\Repositories\GiftExchangeInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GiftService extends BaseService {
    protected $gift;
    protected $giftExchange;
    function __construct(
        GiftInterface $gift,
        GiftExchangeInterface $giftExchange
    ){
        $this->gift = $gift;
        $this->giftExchange = $giftExchange;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->gift->getAllPaginate($perPage, $filter);
    }

    public function getTotalPrice($filter = []){
        return $this->gift->getTotalPrice($filter);
    }

    public function create($data){
        $gift = $this->gift->create($data);
        return $gift;
    }

    public function getByID($id){
        return $this->gift->getByID($id);
    }

    public function updateByID($id, $data){
        $gift = $this->gift->getByID($id);
        if(!$gift){
            return $this->_result(false,  trans('messages.not_found'));
        }
        if ($gift->total_exchange > 0) {
            return $this->_result(false,  trans('gift.can_not_edit_by_had_exchanged'));
        }
        $this->gift->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrIDs){
        foreach($arrIDs as $id) {
            $gift = $this->gift->getByID($id);
            if(!$gift){
                return $this->_result(false,  trans('messages.not_found'));
            }
            if ($gift->total_exchange > 0) {
                return $this->_result(false,  trans('gift.can_not_delete_by_had_exchanged'));
            }
        }

        $result =$this->gift->destroyByIDs($arrIDs);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getActiveListPaginate($perPage = 20, $filter = []){
        // \DB::connection()->enableQueryLog();
        $result = $this->gift->getActiveListPaginate($perPage, $filter);
        // \Log::info('Gift query', \DB::getQueryLog());
        return $result;
    }

    public function exchange($id){
        $user = Auth::user();
        $customerInfo = $user->customer_info()->first();
        if (!$customerInfo) {
            return $this->_result(false,  trans('customer.not_customer'));
        }
        $gift = $this->gift->getByID($id);
        if(!$gift){
            return $this->_result(false,  trans('messages.not_found'));
        }
        if($gift->status == GiftConst::STATUS_UNACTIVE){
            return $this->_result(false,  trans('gift.exchange_failed_by_locked'));
        }
        if($gift->quantity == 0){
            return $this->_result(false,  trans('gift.exchange_failed_by_quantity'));
        }
        $start_datetime = Carbon::parse($gift->start_datetime);
        if(Carbon::now()->lt($start_datetime)){
            return $this->_result(false,  trans('gift.exchange_failed_by_expired'));
        }
        if($gift->end_datetime && $gift->end_datetime != null) {
            $end_datetime = Carbon::parse($gift->end_datetime);
            if(Carbon::now()->gt($end_datetime)){
                return $this->_result(false,  trans('gift.exchange_failed_by_expired'));
            }
        }
        $min_user_badge = $gift->min_user_badge;
        if ($customerInfo->badge < $min_user_badge) {
            return $this->_result(false,  trans('gift.exchange_failed_by_badge'));
        }
        $points = $gift->points;
        if ($customerInfo->total_points < $points) {
            return $this->_result(false,  trans('gift.exchange_failed_by_point'));
        }

        $gift->exchange()->create([
            'user_id'               => $user->id,
            'gift_code'             => $gift->code,
            'gift_name'             => $gift->name,
            'gift_points'           => $gift->points,
            'gift_image_url'        => $gift->image_url,
            'gift_image_mobile_url' => $gift->image_mobile_url,
            'status'                => GiftExchangeConst::STATUS_ACTIVE,
        ]);
        $customerInfo->decrement('total_points', $gift->points);
        $gift->decrement('quantity', 1);
        return $this->_result(true,  trans('gift.exchange_success'));
    }

    public function cancelExchange($id){
        $user = Auth::user();
        $customerInfo = $user->customer_info()->first();
        if (!$customerInfo) {
            return $this->_result(false,  trans('customer.not_customer'));
        }
        $giftExchange = $this->giftExchange->getByID($id);
        if(!$giftExchange){
            return $this->_result(false,  trans('messages.not_found'));
        }
        if($giftExchange->status != GiftConst::STATUS_ACTIVE){
            return $this->_result(false,  trans('gift.return_exchange_failed_by_status'));
        }

        if ($user->id != $giftExchange->user_id) {
            return $this->_result(false,  trans('gift.return_exchange_failed_by_not_right_user'));
        }

        $gift = $this->gift->getByID($giftExchange->gift_id);
        if(!$gift){
            return $this->_result(false,  trans('messages.not_found'));
        }

        $this->giftExchange->updateByID($id, [
            'status'    => GiftExchangeConst::STATUS_RETURN,
            'return_at' => Carbon::now()
        ]);

        $points = $giftExchange->points;
        $customerInfo->increment('total_points', $points);
        $gift->increment('quantity', 1);
        return $this->_result(true,  trans('gift.exchange_success'));
    }
}
