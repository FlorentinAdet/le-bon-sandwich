<?php
namespace lbs\fab\app\models;

class Commande extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'commande';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = true;
       public $incrementing = false;
       protected $keyType = 'string'; 

       public function items(){
              return $this->hasMany('lbs\command\app\models\Item','command_id');
       } 
}
