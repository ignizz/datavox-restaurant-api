<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRol;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends SpatieRol
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name',
        'level',
    ];

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-29
     * @return array<int>
     * Levels for all roles for the top is role number "1" and the lowers are the 2 3 8
     * @example admin = 1, manager= 2, eployee = 3, etc
     */
    const LEVELS = [
        1,2,3,4,5,6,7,8,9
    ];
}
