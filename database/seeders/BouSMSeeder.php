<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;
use App\Models\Techno;
use App\Models\Status;

class BouSMSeeder extends Seeder
{
    public $technos = array(
            array(  "title"  => "информация",
                    "status" => array("информация", "предупреждение", "ошибка"),
                    "level"  => array("common",     "warning",        "failure")
                    ),
            array(  "title"  => "установка заводского номера",
                    "status" => null
                    ),
            array(  "title"  => "смена серийного номера",
                    "status" => null
                    ),
            array(  "title"  => "сборка",
                    "status" => null
                    ),
            array(  "title"  => "лак",
                    "status" => null
                    ),
            array(  "title"  => "термоконтроль +40",
                    "status" => array("пройден", "не пройден"),
                    "level"  => array("ok",      "failure")
                    ),
            array(  "title"  => "термоконтроль -40",
                    "status" => array("пройден", "не пройден"),
                    "level"  => array("ok",      "failure")
                    ),
            array(  "title"  => "термоконтроль +60",
                    "status" => array("пройден", "не пройден"),
                    "level"  => array("ok",      "failure")
                    ),
            array(  "title"  => "термоконтроль -50",
                    "status" => array("пройден", "не пройден"),
                    "level"  => array("ok",      "failure")
                    ),
            array(  "title"  => "прогон",
                    "status" => array("начат",  "пройден", "не пройден"),
                    "level"  => array("common", "ok",      "failure")
                    ),
            array(  "title"  => "поставка",
                    "status" => array("первичная",  "втоичная", "замена"),
                    "level"  => array("common",     "common",   "common")
                    ),
            array(  "title"  => "возврат",
                    "status" => array("рекламация",  "ремонт",  "замена",  "другое"),
                    "level"  => array("failure",     "warning", "common",  "common")
                    ),
             array( "title"  => "ремонт",
                    "status" => array("выполнен",  "не выполнен"),
                    "level"  => array("ok",        "failure")
                    ),
        );
    
    public function addBouSM()
    {
        // пустой продукт
        $db = new Product;
        $db->title       = "none";
        $db->description = "";
        $db->path        = "";
        $db->save();

        $db = new Product;
        $db->title       = "БОУ-СМ";
        $db->description = "БОУ для комплекса ПВО Панцирь-СМ, заказчик КБП";
        $db->path        = "\storage\uploads\bou-sm.jpg";
        $db->save();
        return $db->id;
    }

    public function addTechnos($product_id, $technos)
    {
        $p= Product::where('title', 'none')->first();

        // пустой техпроцесс
        $t = new Techno;
        $t->product_id = $p->id;
        $t->title      = 'none';
        $t->order      = 0;
        $t->save();
        // пустой статус
        $st = new Status;
        $st->techno_id = $t->id;
        $st->title     = 'none';
        $st->level     = 'common';
        $st->order     = 0;
        $st->save();

        foreach ($technos as $key => $t) {
            $db = new Techno;
            $db->product_id = $product_id;
            $db->title      = $t["title"];
            $db->order      = 100 * $key;
            $db->save();
            $t_id= $db->id;
            if ($t["status"]) {
                foreach ($t["status"] as $key => $title) {
                    $st = new Status;
                    $st->techno_id = $t_id;
                    $st->title     = $title;
                    $st->level     = $t["level"][$key];
                    $st->order     = 10 * $key;
                    $st->save();
                }
            }
        }
    }

    public function run()
    {
        $this->addTechnos($this->addBouSM(),  $this->technos);
    }
}
