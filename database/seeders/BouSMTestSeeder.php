<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Techno;
use App\Models\Status;
use App\Models\Product;
use App\Models\Number;
use App\Models\User;

class BouSMTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        function IDX($x) {
            if (strlen($x) == 1) {
                return ord($x) - ord("A");
            } else {
                return 26 * (ord($x) - ord("A") + 1) + ord($x[1]) - ord("A");
            }
        }

        function TOHTML($a) {
            $a= '<p>'.$a.'</p>';
            return str_replace('#', '</p><p>',$a);
        }

        DB::transaction(function () {
            $hlog = fopen("./log.txt", "at");
            fwrite($hlog, '---------------------------------------' . PHP_EOL);
            fwrite($hlog, '-            BouSM Test Seader        -' . PHP_EOL);
            fwrite($hlog, '---------------------------------------' . PHP_EOL);
    
            $product   = Product::where('title',"БОУ-СМ")->first();
            $user      = User::where('name','admin')->first();
            $test_p    = Techno::where("title","термоконтроль +60")->first();
            $test_p_ok = Status::where([["techno_id",$test_p->id],["level","ok"]])->first();
            $test_p_fi = Status::where([["techno_id",$test_p->id],["level","failure"]])->first();
            $test_m    = Techno::where("title","термоконтроль -50")->first();
            $test_m_ok = Status::where([["techno_id",$test_m->id],["level","ok"]])->first();
            $test_m_fi = Status::where([["techno_id",$test_m->id],["level","failure"]])->first();

            $handle = fopen("./Test-sm.csv", "r");

            if ($handle) {
                // фиктивное чтение заголовков
                fgets($handle, 4096);
                fgets($handle, 4096);

                // читаем основные записи до конц файла
                $items= array();
                $sn= -1;
                $n= 0;
                while (($buffer = fgets($handle, 4096)) !== false) {
//                    $buffer = iconv('windows-1251', 'utf-8', $buffer);
                    
                    if (count($items) == 0) {
                        $items= array_merge($items, str_getcsv($buffer, ";"));
                    } else {
                        $arr1= $items;
                        $arr2= str_getcsv($buffer, ";");
                        $arr1[count($arr1)-1]= nl2br($arr1[count($arr1)-1] . $arr2[0]);
                        for ($i = 1; $i < count($arr2); $i++) {
                            array_push($arr1, $arr2[$i]);
                        }
                        $items= $arr1;
                    }

//                    fwrite($hlog, $n.':'.$buffer);
//                    fwrite($hlog, $n.':'.implode('$$', $items)."    -".count($items).PHP_EOL);
                    
                    if (count($items) < 10) {
                        fwrite($hlog, '==добор=='.$n.':'.implode('$$', $items)."    -".count($items).PHP_EOL);
                        $items[count($items)-1]= nl2br($items[count($items)-1]);
                        continue;
                    }
                    $n++;
                    fwrite($hlog, $n.':'.implode('$$', $items)."    -".count($items).PHP_EOL);

                    if (empty($items[IDX("D")]) && empty($items[IDX("E")]) && empty($items[IDX("F")]) && 
                        empty($items[IDX("G")]) && empty($items[IDX("H")]) && empty($items[IDX("I")]) && 
                        empty($items[IDX("J")])) 
                    {
                        $items=array();
                        continue;
                    }
                   
                    if (is_numeric($items[IDX("A")])) {
                        $sn= intval($items[IDX("A")]);
                    }

                    //---------------------------------------
                    // событие
                    //---------------------------------------
                    $date= '1990-01-01';
                    if (!empty($items[IDX("F")])) {
                        $d= explode('.',$items[IDX("F")]);
                        if (count($d) == 3) $date= "20".$d[2]."-".$d[1]."-".$d[0];
                    } 
                    
                    $text= '';
                    $s_id= 0;
                    $t_id= 0;
                    if ($items[IDX("C")] == "До лака") {
                        $t_id= Techno::where("title","сборка")->first()->id;
                    } else
                    if ($items[IDX("C")] == "После лака") {
                        $t_id= Techno::where("title","лак")->first()->id;
                    } else
                    if ($items[IDX("C")] == "После прогона") {
                        $t_id= Techno::where("title","прогон")->first()->id;
                    } else
                    if ($items[IDX("C")] == "После ремонта") {
                        $t_id= Techno::where("title","ремонт")->first()->id;
                        $s_id= Status::where([["techno_id",$t_id],["level","ok"]])->first()->id;
                    } else
                    if ($items[IDX("C")] == "После сборки") {
                        $t_id= Techno::where("title","сборка")->first()->id;
                    } else
                    if ($items[IDX("C")] == "Проверка") {
                    } else
                    if ($items[IDX("C")] == "Прогон") {
                        $t_id= Techno::where("title","прогон")->first()->id;
                    } else
                    if ($items[IDX("C")] == "Разбор") {
                        $t_id= Techno::where("title","информация")->first()->id;
                        $text= 'Разбор';
                        $s_id= Status::where([["techno_id",$t_id],["level","warning"]])->first()->id;;
                    } else
                    if ($items[IDX("C")] == "Ремонт") {
                        $t_id= Techno::where("title","информация")->first()->id;
                        $text= 'Разбор';
                        $s_id= Status::where([["techno_id",$t_id],["level","warning"]])->first()->id;;
                    } else {
                        $t_id= Techno::where("title","информация")->first()->id;
                        $text= $items[IDX("C")];
                        $s_id= Status::where([["techno_id",$t_id],["level","failure"]])->first()->id;;
                    }

                    if ($t_id > 0) {
                        $event= new Event;
                        $event->date        = $date;
                        $event->product_id  = $product->id;
                        $event->sn_p        = -1;
                        $event->sn_m        = -1;
                        $event->sn_n        = $sn;
                        $event->description = '<p>'.$text.'</p>';
                        $event->techno_id   = $t_id;
                        $event->status_id   = $s_id;
                        $event->active      = true;
                        $event->user_id     = $user->id;
                        $event->save();
//                        fwrite($hlog, $n.':'.$sn.'#'.$text.PHP_EOL);
                    }

                    //---------------------------------------
                    // -50
                    //---------------------------------------
                    $t_id= $test_m->id;
                    $date= '1990-01-01';
                    if (!empty($items[IDX("F")])) {
                        $d= explode('.',$items[IDX("F")]);
                        if (count($d) == 3) $date= "20".$d[2]."-".$d[1]."-".$d[0];
                    } 
                    
                    $text= "t°C=".$items[IDX("D")];
                    if ($items[IDX("E")] == "нет") {
                        $s_id= $test_m_ok->id;
                    } else {
                        $s_id= $test_m_fi->id;
                        $text.= " - ".$items[IDX("E")];
                    }

                    $event= new Event;
                    $event->date        = $date;
                    $event->product_id  = $product->id;
                    $event->sn_p        = -1;
                    $event->sn_m        = -1;
                    $event->sn_n        = $sn;
                    $event->description = '<p>'.$text.'</p>';
                    $event->techno_id   = $t_id;
                    $event->status_id   = $s_id;
                    $event->active      = true;
                    $event->user_id     = $user->id;
                    $event->save();
//                    fwrite($hlog, $n.':'.$sn.'#'.$text.PHP_EOL);

                    //---------------------------------------
                    // +60
                    //---------------------------------------
                    $t_id= $test_p->id;
                    $date= '1990-01-01';
                    if (!empty($items[IDX("J")])) {
                        $d= explode('.',$items[IDX("J")]);
                        if (count($d) == 3) $date= "20".$d[2]."-".$d[1]."-".$d[0];
                    } 
                    
                    $text= "t°C=".$items[IDX("G")];
                    if ($items[IDX("I")]) {
                        $text.="..".$items[IDX("I")];
                    }
                    if ($items[IDX("H")] == "нет") {
                        $s_id= $test_p_ok->id;
                    } else {
                        $s_id= $test_p_fi->id;
                        $text.= " - ".$items[IDX("H")];
                    }

                    $event= new Event;
                    $event->date        = $date;
                    $event->product_id  = $product->id;
                    $event->sn_p        = -1;
                    $event->sn_m        = -1;
                    $event->sn_n        = $sn;
                    $event->description = '<p>'.$text.'</p>';
                    $event->techno_id   = $t_id;
                    $event->status_id   = $s_id;
                    $event->active      = true;
                    $event->user_id     = $user->id;
                    $event->save();
//                    fwrite($hlog, $n.':'.$sn.'#'.$text.PHP_EOL);


                    $items= array();
                }
                
                fclose($handle);
                fclose($hlog);    
            }
        });

    }
}
