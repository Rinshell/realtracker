<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;
    

    protected $fillable = ['date', 'product_id', 'sn_n','sn_m','sn_p', 'description', 'techno_id', 'status_id', 'user_id', 'active'];
    protected $with = ['files'];

    public function files() {
        return $this->hasMany('App\Models\File', 'event_id');
    }
}
