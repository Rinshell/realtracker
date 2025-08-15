<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Number;
use App\Models\Techno;
use App\Models\Status;
use App\Models\User;
use App\Models\Product;

use Carbon\Carbon;

class EventNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавляет event из таблицы Numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user= User::role('admin')->first();
        $this->info('user id:' . $user->id . ' name:' . $user->name);

        $p_id= Product::where('title', 'БОУ-СМ')->first()->id;
        $t_id= Techno::where([["title","установка заводского номера"],['product_id',$p_id]])->first()->id;

        foreach(Number::all() as $n) {
            $date           = Carbon::parse($n->created_at)->locale('ru')->isoFormat('YYYY-MM-DD');
            $evsn= Event::where('sn_n', $n->serial)->get()->last();

            $this->info($evsn->sn_n . ': pid=' . $p_id . ': tid=' . $t_id . '    date=' . $date . ' : ' . $evsn->date);
            $ev= new Event;
            $ev->date       = $date;
            $ev->product_id = $p_id;
            $ev->sn_n       = $evsn->sn_n;
            $ev->sn_m       = $evsn->sn_m;
            $ev->sn_p       = $evsn->sn_p;
            $ev->description= $n->factory;
            $ev->techno_id  = $t_id;
            $ev->status_id  = Status::where("title","none")->first()->id;
            $ev->active     = true;
            $ev->user_id    = $user->id;
            $ev->save();
        }
    }
}
