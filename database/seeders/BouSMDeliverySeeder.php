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

class BouSMDeliverySeeder extends Seeder
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
            $hlog = fopen("./log.txt", "w+");
            fwrite($hlog, '---------------------------------------' . PHP_EOL);
            fwrite($hlog, '-        BouSM Delivery Seader        -' . PHP_EOL);
            fwrite($hlog, '---------------------------------------' . PHP_EOL);
    
            $product   = Product::where('title',"БОУ-СМ")->first();
            $user      = User::where('name','admin')->first();

            fwrite($hlog, 'product:'. $product->id . '   user:' . $user->name . PHP_EOL);
            $dbg= 7; // mask for write log 1-ERR, 2-WRN, 4-INF

            $handle = fopen("./Поставки_ремонт.csv", "r");

            if ($handle) {
                // фиктивное чтение заголовков
                fgets($handle, 4096);
                fgets($handle, 4096);

                // читаем основные записи до конц файла
                $items= array();
                $sn= -1;
                $fn= -1;
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
                    
                    if (count($items) < 16) {
//                        fwrite($hlog, '==добор=='.$n.':'.implode('$$', $items)."    -".count($items).PHP_EOL);
                        $items[count($items)-1]= nl2br($items[count($items)-1]);
                        continue;
                    }
/*                    if (count($items) != 16) {
                        if ($dbg & 1) fwrite($hlog, '===' . $n.'>:'.implode(';', $items)."    -".count($items).PHP_EOL);
                        continue;
                    }*/
                    $n++;
                    fwrite($hlog, '==='. $n.':'.implode(';', $items)."    -".count($items).PHP_EOL);

                    // ИБВТ
                    $ibvt= $items[IDX("D")];
                    if (strpos($ibvt,"466226.011")===false && strpos($ibvt,"466922.001")===false) 
                    {
                        if ($dbg & 2) fwrite($hlog, '    WRN ibvt'.PHP_EOL);
                        $items=array();
                        continue;
                    }
                    // Зав. номер
                    $fn= $items[IDX("A")];

                    // дата поставки
                    if (!empty($items[IDX("F")])) {
                        $d= explode('.',$items[IDX("F")]);
                        if (count($d) == 3) {
                            $date= ($d[2] > 1000 ? $d[2] : "20".$d[2]) . "-".$d[1]."-".$d[0];
                        }
                    }
                    // дата изготовления
                    if (!empty($items[IDX("C")])) {
                        $d= explode('.',$items[IDX("C")]);
                        if (count($d) == 3 && is_numeric($d[0])) {
                            $birthday= ($d[2] > 1000 ? $d[2] : "20".$d[2]) . "-".$d[1]."-".$d[0];
                        } else {
                            $birthday= $items[IDX("C")];
                        }
                    }
                    // серийный номер
                    if (is_numeric($items[IDX("B")])) {
                        $sn= intval($items[IDX("B")]);
                    } else {
                        if ($dbg & 1) fwrite($hlog, '    ERR sn:' . $items[IDX("B")] . PHP_EOL);
                        $items=array();
                        continue;
                    }

                    // текст
                    $text= '';
                    if ($items[IDX("E")]) $text.= "Договор:".$items[IDX("E")];
                    if ($items[IDX("G")]) $text.= ($text ? '<br />' : '') . 'Заказчик:' . $items[IDX("G")];
                    if ($birthday) $text.= ($text ? '<br />' : '') . 'Изг:' . $birthday;
                    if ($items[IDX("H")]) $text.= ($text ? '<br />' : '') . $items[IDX("H")];
                    $text= preg_replace('{(<br[\\s]*(>|\/>)\s*){2,}}','$1',nl2br($text));

                    if (strpos($birthday,"ремонт") !== false || strpos($birthday,"рек.акт") !== false || strpos($items[IDX("H")],"ремонт") !== false) {
                        $t_id= Techno::where("title","ремонт")->first()->id;
                        $s_id= Status::where([["techno_id",$t_id],["level","ok"]])->first()->id;
                    } else {
                        $t_id= Techno::where("title","поставка")->first()->id;
                        $s_id= Status::where("techno_id",$t_id)->first()->id;
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
                        if ($dbg & 4) fwrite($hlog, '    INF write event: sn=' . $sn . ' ' . $date . ' tid=' . $t_id .  ' text=' . $text . PHP_EOL);
                    }

                    if (strpos($birthday,"ремонт")===false && strpos($birthday,"рек.акт")===false) {
                        $num= new Number;
                        $num->product_id= $product->id;
                        $num->factory= $fn;
                        $num->serial= $sn;
                        $num->save();
                        if ($dbg & 4) fwrite($hlog, '    INF write number ' . $sn . ' - ' . $fn . PHP_EOL);
                    }

                    $items= array();
                }
                
                fclose($handle);
                fclose($hlog);    
            }
        });

    }
}
