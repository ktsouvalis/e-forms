<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Department;
use App\Models\MicroappUser;
use App\Models\FilecollectUser;
use App\Models\UsersOperations;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'display_name',
        'username',
        'email',
        'password',
        'department_id',
        'telephone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function operations()
    {
        // return $this->hasMany(UsersOperations::class);
         return $this->hasMany(UsersOperations::class)
                ->join('operations', 'users_operations.operation_id', '=', 'operations.id')
                ->orderBy('operations.menu_priority');
    }

    public function forms(){
        return $this->hasMany(FormUser::class);
    }

    public function microapps(){
        return $this->hasMany(MicroappUser::class);
    }

    public function posts()
    {
        return $this->morphMany(TicketPost::class, 'ticketer');
    }

    public function superadmin(){
        return $this->hasOne(Superadmin::class);
    }

    public static function isAdmin(){
        return Superadmin::where('user_id',Auth::id())->exists(); 
    }

    public static function isDirector(){
        return (Auth::user()->department->name == 'Διευθυντής Εκπαίδευσης');
    }

    public function addedbys()
    {
        return $this->morphMany(FileshareStakeholder::class, 'addedby');
    }
}
