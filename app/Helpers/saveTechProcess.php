
<?php

use App\Models\Techno;
use App\Models\Status;

// хелпер function_exists() гарантирует, что функция будет доступна только в том случае, если она не существует в экземпляре приложения.
if (!function_exists('saveTechProcess')) {
    function saveTechProcess($buildTechno, $product_id) {
        $status_ids= array();
        $techno_ids= array();
        $t_idx= 0;
        foreach ($buildTechno as $t) {
            if (array_key_exists('id', $t)) {
                $techno= Techno::find($t['id']);
            } else {
                $techno= new Techno;
                $techno->product_id = $product_id;
            }
            $techno->title = $t['title'];
            $techno->order = $t_idx;
            $techno->save();
            $s_idx= 0;
            foreach ($t['status'] as $s) {
                if (array_key_exists('id', $s)) {
                    $status= Status::find($s['id']);
                } else {
                    $status= new Status;
                    $status->techno_id = $techno->id;
                }
                $status->title = $s['title'];
                $status->level = $s['level'];
                $status->order = $s_idx;
                $status->save();
                $s_idx++;
                array_push($status_ids, $status->id);
            }
            $t_idx++;
            array_push($techno_ids, $techno->id);
        }

        Log::debug($status_ids);

        // delete extra status
        foreach (Techno::where('product_id',$product_id)->get() as $t) {
            foreach (Status::where('techno_id',$t->id)->get() as $s) {
                Log::debug('Status model ids:' . $s->id);
                if (!in_array($s->id, $status_ids)) {
                    $s->delete();
                    Log::debug('Delete Status id:' . $s->id);
                }
            }
        }
    }
}
