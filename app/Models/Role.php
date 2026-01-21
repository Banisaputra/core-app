<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public static function getUserRole($userID) {
        $userRole = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $userID)
            ->select('roles.*')
            ->get();
        return $userRole;
    }

    public function asignRole($userID) {
        DB::table('role_user')
        ->where('role_user.user_id', $userID)
        ->delete();

        DB::table('role_user')->insert([
            'user_id' => $userID,
            'role_id' => $this->id,
        ]);
    }
}
