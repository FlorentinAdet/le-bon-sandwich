<?php
namespace lbs\fab\app\models;

class Item extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'item';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;
}
