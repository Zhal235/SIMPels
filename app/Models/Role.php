<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * Get the guard name for the role.
     *
     * @return string
     */
    public function getGuardName()
    {
        return $this->guard_name;
    }

    /**
     * Get the role name.
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->name;
    }
}
