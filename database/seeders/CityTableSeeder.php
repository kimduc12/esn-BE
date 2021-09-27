<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->truncate();
        DB::table('districts')->truncate();
        DB::table('wards')->truncate();
        $jsonPath = storage_path() . "/app/city/index.json";
        $dataArray = json_decode(file_get_contents($jsonPath));

        foreach ($dataArray as $item_key => $item) {
            $city = City::create([
                'name' => $item_key,
                'code' => $item->code
            ]);
            $districtJsonPath = storage_path() . "/app/city/" . $item->file_path;
            $dataDt = json_decode(file_get_contents($districtJsonPath));
            foreach ($dataDt->district as $dt) {
                $districtCode = $item->code.'-'.Str::upper(Str::slug($dt->name, ''));
                //dd($districtCode);
                $district = $city->districts()->create(['name' => $dt->pre . " " . $dt->name, 'code' => $districtCode]);
                //dd($dt->ward);
                foreach ($dt->ward as $w) {
                    $wardCode = $districtCode.'-'.Str::upper(Str::slug($w->name, ''));
                    try {
                        $district->wards()->create(['name' => $w->pre . " " . $w->name, 'code' => $wardCode]);
                    } catch (\Exception $e) {}
                }
                $district->wards()->create(['name' => 'Huyện khác', 'code' => $districtCode.'-HUYEN-KHAC']);
            }
            $city->districts()->create(['name' => 'Quận khác', 'code' => $item->code.'-QUAN-KHAC']);
        }

    }
}
