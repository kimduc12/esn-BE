<?php
namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Services\FileService;

class UploadController extends RestfulController
{

    protected $fileService;

    public function __construct(FileService $fileService){
        parent::__construct();
        $this->fileService = $fileService;

    }

    /**
     * Upload file
     * @group File management
     * @authenticated
     * @bodyParam file file required
     * @bodyParam thumb file
     * @bodyParam module string required Example: product|blog|gift
     * @response {
            *   "status": true,
            *   "data": {
                *   "id": 1,
                *   "type": "image",
                *   "file_path": "/storage/product/2021/02/03/test-18944-1612373493.png",
                *   "file_ext": "png",
                *   "thumb_path": "/storage/product/2021/02/03/test-18944-1612373493.png",
                *   "thumb_ext": "png"
            *   }
        *   }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function uploadStorage(Request $request){
        $this->validate($request, [
            'file'   => 'required|file|max:5120',
            'thumb'  => 'nullable|file|max:5120',
            'module' => 'required|in:product,blog,gift',
        ]);
        try{
            if(!$request->hasFile('file')){
                return $this->_error(trans('messages.images_not_found'));
            }
            $images = $request->file('file');
            $module = $request->input('module');
            $thumb = null;
            if($request->hasFile('thumb')){
                $thumb = $request->file('thumb');
            }
            $file = $this->fileService->uploadStorage($images, $module, $thumb);
            return $this->_response($file);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }

    }

    /**
     * Delete file
     * @group File management
     * @authenticated
     * @bodyParam file_url string required Example: /storage/product/2021/02/09/blob-14877-1612877181.jpg
     * @response {
            *   "status": true,
            *   "message": "Deleted"
        *   }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function deleteFile(Request $request){
        $this->validate($request, [
            'file_url'   => 'required'
        ]);
        try{
            $input = $request->all();
            $result = $this->fileService->deleteFile($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }

    }

}
