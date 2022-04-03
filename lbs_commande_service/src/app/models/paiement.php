<?php
namespace lbs\command\app\models;

class Paiement extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'paiement';  /* le nom de la table */
       protected $primaryKey = 'reference';     /* le nom de la clé primaire */
       public    $timestamps = true;
       public $incrementing = false;
       protected $keyType = 'string'; 
}
