<?php
namespace lbs\auth\api\models;

class User extends \Illuminate\Database\Eloquent\Model{

       protected $table      = 'user';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = true;
}
