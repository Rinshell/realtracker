<?php
/**
 * для удаления БД
 * php artisan migrate:fresh --seed --seeder=BouSMSeeder
 * 
 * для заполнения из wiki
 * php artisan db:seed --class=BouSMWikiSeeder
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \DateTime;

use App\Models\Event;
use App\Models\Techno;
use App\Models\Status;
use App\Models\Product;
use App\Models\Number;
use App\Models\User;

class BouSMWikiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function getSN($idx, $sn) 
    {
        if (count($sn) == 3) return $sn[$idx];
        if (count($sn) == 1 && $idx == 2) return $sn[0];
        return $idx == 2 ? '0000' : '000';
    }

    public function run()
    {
        $hlog = fopen("./log.txt", "at");

        $product= Product::where('title',"БОУ-СМ")->first();
        $user= User::where('name','admin')->first();

        $text = file_get_contents('./bou-sm.txt');
        $state= 0;
        $sn= null;
        $posdbg= null;
        $ad= array();
        $action= array();
        $count= 0;
        $fn= null;
        foreach (mb_split(PHP_EOL, $text) as $t) {
            $count++;
            $t= trim(preg_replace("/\s+/", " ", $t));

            if ($state == 1) {
                // ищем таблицу
                if (strlen($t) && $t[0] == '^') {
                    $state= 2;
                } else {
                    if ($info == null) $info= '';
                    $info= $info . '<p>' . $t . '</p>';
                }
            } elseif ($state == 2) {
                // разбор события
                if (strlen($t) && $t[0] == '|' && mb_strpos($t, "|", 2) < 12) {
                    $pos = mb_strpos($t, "|", 2);
                    $ad= mb_split('\.', mb_substr($t, 1, $pos-1));
                    $d = new DateTime();
                    if (count($ad) == 3) {
                        $d->setDate(2000 + intval($ad[2]), intval($ad[1]), intval($ad[0]));
                    } else {
                        $d->setDate(2000 + intval($ad[1]), intval($ad[0]), 1);
                    }
                    $pos1 = mb_strpos($t, "|", $pos+1);
                    $sna= mb_split('-', trim(mb_substr($t, $pos+1, $pos1 - $pos - 1)));
                    if (count($sna) == 3) {
                        if ($sn === null) $sn= $sna;
                        $r= array_diff_assoc($sn, $sna);
                        $posdbg= implode(" ", $sn)."#".implode(" ", $sna).'#'.implode(" ", $r).'#';
                        if (count($r)) {
                            // смена серийного номера
                            $action[]= array('date' => $d, 
                                             'sn'   => $sna, 
                                             'text' => "смена серийного номера на {".$this->getSN(0, $sna)."-".$this->getSN(1, $sna)."-".$this->getSN(2, $sna)."}");
                            $sn= $sna;
                        }
                    } else {
//                        $action[]= array('date' => $d, 'sn' => $sn, 'text' => 'ERROR:'.implode('-',$sna));
                    }
                    $pos2 = mb_strpos($t, "|", $pos1+1);
                    $txt = trim(str_replace('\\', '<br>', mb_substr($t, $pos1+1, $pos2 - $pos1- 1)));
                    $action[]= array('date' => $d, 'sn' => $sn, 'text' => $txt, 'dbg' => $posdbg);
                } else {
                    $pos = mb_strpos($t, "=====");
                    if ($pos !== false) {
                        $dbg= $pos;
                        $state= 0;
                        if ($fn) {
                            $num= new Number;
                            $num->product_id= $product->id;
                            $num->factory= $fn;
                            $num->serial= intval($this->getSN(2, $action[0]['sn']));
                            $num->save();
                        }
                        // записываем все в БД
                        if ($info) {
                            fwrite($hlog,  $action[0]['date']->format('d-m-Y') . '*' . $action[0]['sn'][0]);
                            if (count($action[0]['sn']) > 1) fwrite($hlog,  '-'.$action[0]['sn'][1]);
                            if (count($action[0]['sn']) > 2) fwrite($hlog,  '-'.$action[0]['sn'][2]);
                            fwrite($hlog,  '*'.$info.'*'.'dbg:'.$dbg.PHP_EOL);

                            $techno= Techno::where([['product_id', $product->id],['title', 'информация']])->first();
                            $status= Status::where([['techno_id',  $techno->id],['title', 'информация']])->first();
                            $event= new Event;
                            $event->date= $action[0]['date']->format('Y-m-d');
                            $event->product_id= $product->id;
                            $event->sn_p= intval($this->getSN(0, $action[0]['sn']));
                            $event->sn_m= intval($this->getSN(1, $action[0]['sn']));
                            $event->sn_n= intval($this->getSN(2, $action[0]['sn']));
                            $event->description= $info;
                            $event->techno_id= $techno->id;
                            $event->status_id= $status ? $status->id : null;
                            $event->active= true;
                            $event->user_id= $user->id;
                            $event->save();

                        }
                        foreach ($action as $a) {
                            fwrite($hlog,  $a['date']->format('d-m-Y') . '*' . $a['sn'][0]);
                            if (count($a['sn']) > 1) fwrite($hlog,  '-'.$a['sn'][1]);
                            if (count($a['sn']) > 2) fwrite($hlog,  '-'.$a['sn'][2]);
                            fwrite($hlog,  '*'.$a['text'].PHP_EOL);

                            $t_id= 0;
                            $s_id= 0;
                            if (mb_strpos($a['text'], "смена серийного номера") !== false) {
                                $t_id= Techno::where([['product_id', $product->id],['title', "смена серийного номера"]])->first()->id;
                                $s_id= Status::where('title', 'none')->first()->id;
                            } else {
                                $t_id= Techno::where([['product_id', $product->id],['title', 'информация']])->first()->id;
                                $s_id= Status::where([['techno_id',  $techno->id],['title', 'информация']])->first()->id;
                            }

                            $event= new Event;
                            $event->date= $a['date']->format('Y-m-d');
                            $event->product_id= $product->id;
                            $event->sn_p= intval($this->getSN(0, $a['sn']));
                            $event->sn_m= intval($this->getSN(1, $a['sn']));
                            $event->sn_n= intval($this->getSN(2, $a['sn']));
                            $event->description= '<p>'.$a['text'].'</p>';
                            $event->techno_id= $t_id;
                            $event->status_id= $s_id;
                            $event->active= true;
                            $event->user_id= $user->id;
                            $event->save();
                        }
            
                    } elseif (strlen($t) && $t[0] != PHP_EOL) {
                        if ($info == null) $info= '';
                        $tmpl= array('|','**','\\');
                        $rpl = array( '',  '','<br>');
                        $str= str_replace($tmpl, $rpl, $t);
                        $info= $info . '<p>' . $t . '</p>';
                    }
                }

            }
            if ($state === 0) {
                $info= null;
                // ищем заголовок
                $pos = mb_strpos($t, "=====");
                if ($pos === false) continue;
                $pos = mb_strpos($t, "Заводской номер", $pos);
                $pos+= $pos === false ? 5 : 15;
                $pos1 = mb_strpos($t, "=====", $pos);
                $pos2 = mb_strpos($t, "(", $pos);
                if ($pos2 !== false) {
                    $pos3 = mb_strpos($t, ")", $pos2);
                    $info = '<p>'.mb_substr($t, $pos2+1, $pos3-$pos2-1).'</p>';
                    $pos1 = $pos2;
                }
                $fn= mb_substr($t, $pos+1, $pos1 - $pos -2);
                if ($fn == 'XXX') $fn= null;

                fwrite($hlog, '---------------------------------------' . PHP_EOL);
                fwrite($hlog, 'FN:%' . $fn . PHP_EOL);
                $state= 1;
                $action= array();
                $sn= null;
            }            
        }

        fclose($hlog);
    }
}
