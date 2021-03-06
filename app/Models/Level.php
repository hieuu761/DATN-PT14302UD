<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Level extends Model
{
    protected $table = "level";
    protected $primaryKey = "id_level";
    public $timestamps = false;

    public function getAll() {
        return GiftCode::orderBy('id_level','ASC')
            ->get();
    }
}
