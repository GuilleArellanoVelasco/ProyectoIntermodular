<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //Desactivar las timestamps porque no se usan en la tabla
    public $timestamps = false;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    //Relacion de roles con usuarios
    public function users(){
        return $this->belongsToMany(User::class);
    }


}
