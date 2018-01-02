<?php

namespace App\Models\Web;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'oracle';
    protected $table = "v_api_user";
    //protected $table = "sma_user_m";
    //protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $visible = ['sys', 'co', 'id', 'name', 'class', 'state'];
    protected $hidden = ['pwd', 'password'];
    
    public function getRememberToken()
    {
        return null; // not supported
    }

    public function setRememberToken($value)
    {
        // not supported
    }

    public function getRememberTokenName()
    {
        return null; // not supported
    }
    
    /**
    * Overrides the method to ignore the remember token.
    */
    public function setAttribute($key, $value)
    {
        
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute)
        {
            parent::setAttribute($key, $value);
        }
    }
}
