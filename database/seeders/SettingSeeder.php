<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            ["id" => "HODLPM", "title" => "Ariep Jaenul, S.Pd. M.Sc.Eng", "content" => "S092019030004"],
            ["id" => "CONTACT", "title" => "WhatsApp", "content" => "087880004742"],
            ["id" => "MINSCORE", "title" => "KKM", "content" => "70"],
            ["id" => "TOTALAUDITOR", "title" => "Total Auditor", "content" => "2"],
            ["id" => "LINKINSTRUMENT", "title" => "Klik Disini", "content" => "https://www.banpt.or.id/wp-content/uploads/2019/10/Lampiran-5-PerBAN-PT-5-2019-tentang-IAPS-Pedoman-Penilaian.pdf"],
            ["id" => "INFO", "title" => "Y", "content" => "This system is under development. Any information and designs on this system are not final and LPM will not be responsible for any loss or damage caused by the use of information obtained from this system."],
        ];

        foreach ($data as $x) {
            if(!Setting::where('id', $x['id'])->first()){
                $m = new Setting();
                $m->id = $x['id'];
                $m->title = $x['title'];
                $m->content = $x['content'];
                $m->save();
            }
        }
    }
}
