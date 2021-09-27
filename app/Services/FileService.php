<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Repositories\FileInterface;

class FileService  extends BaseService {
    protected $file;
    function __construct(
        FileInterface $file
    ){
        $this->file = $file;
    }

    public function uploadStorage($file, $module, $thumb = null)
    {
        $dirUpload = date('Y')."/".date('m')."/".date('d');
        $name = $file->getClientOriginalName();
        $nameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);
        $size = $file->getSize();
        $ext = $file->extension();
        $newName = $nameWithoutExtension."-".$size."-".strtotime("now").".".$ext;
        $file_path = "/storage/".$file->storeAs($module."/".$dirUpload, $newName, 'public');
        // Possible values:
        // 0 = unknown
        // 1 = text
        // 2 = image
        // 3 = audio
        // 4 = video
        // 5 = application
        $type = 2;
        $data = [
            'type'      => $type,
            'file_path' => $file_path,
            'file_ext'  => $ext,
        ];
        if ($thumb != null) {
            $thumb_name = $thumb->getClientOriginalName();
            $thumbNameWithoutExtension = pathinfo($thumb_name, PATHINFO_FILENAME);
            $thumb_size = $thumb->getSize();
            $thumb_ext = $thumb->extension();
            $newThumbName = $thumbNameWithoutExtension."-".$thumb_size."-".strtotime("now").".".$thumb_ext;
            $thumb_path = "/storage/".$thumb->storeAs($module."/".$dirUpload, $newThumbName, 'public');
            $data['thumb_path'] = $thumb_path;
            $data['thumb_ext'] = $thumb_ext;
        }
        return $this->file->create($data);
    }

    public function deleteFile($input)
    {
        $file_url = $input['file_url'];
        $file_url = str_replace("/storage/", "", $file_url);
        $res = Storage::disk('public')->delete($file_url);
        if (!$res) {
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

}
