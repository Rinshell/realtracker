<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Techno;
use App\Models\Product;

use Carbon\Carbon;

class SnUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sn:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Наводим порядок с серийными номерами';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $p_id= Product::where('title', 'БОУ-СМ')->first()->id;
        $t_id= Techno::where([["title","установка серийного номера"],['product_id',$p_id]])->first()->id;
        for ($sn= 0; $sn < 1000; $sn++) {
            $events= Event::where('sn_n', $sn)->get()->sortBy('date');
            if ($events->count() == 0) continue;

            $evsn= $events->first();
            $date= intval(Carbon::parse($evsn->date)->locale('ru')->isoFormat('YYYY'));
            if ($date >= 2020 && ($evsn->sn_p == -1 || $evsn->sn_m == -1)) {
                $sn_n= $evsn->sn_n;
                $sn_m= $evsn->sn_m == -1 ? 0 : $evsn->sn_m;
                $sn_p= $evsn->sn_p == -1 ? 6 : $evsn->sn_p;
            } else {
                $sn_n= $evsn->sn_n;
                $sn_m= $evsn->sn_m;
                $sn_p= $evsn->sn_p;
            }
            $this->info('* ' . $evsn->sn_n . ' ' . $evsn->date . '   ' . $evsn->sn_p . '-' . $evsn->sn_m . '-' . $evsn->sn_n  . '   ' . $sn_p . '-' . $sn_m . '-' . $sn_n);
            foreach ($events as $ev) {
                if ($ev->techno_id == $t_id) {
                    $sn_n= $ev->sn_n;
                    $sn_m= $ev->sn_m;
                    $sn_p= $ev->sn_p;
                    $this->info('    ' . $ev->sn_n . ' ' . $ev->date . ': ' . $ev->sn_p . '-' . $ev->sn_m . '-' . $ev->sn_n);
                } else {
                    $ev->sn_n   = $sn_n;
                    $ev->sn_m   = $sn_m;
                    $ev->sn_p   = $sn_p;
                    $ev->save();
                }
            }
        }        
    }
}
