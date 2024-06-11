<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Request;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    static public function getSingle($id)
    {
        return self::find($id);
    }

    static public function getAdmin()
    {
        // Build the initial query
        $query = self::select('users.*')
            ->where('user_type', '=', 1)
            ->where('is_delete', '=', 0);

        // Apply filters
        if(!empty(Request::get('email'))){
            $query->where('email', 'like', '%'.Request::get('email').'%');
        }
        else if(!empty(Request::get('name'))){
            $query->where('name', 'like', '%'.Request::get('name').'%');
        }
        else if(!empty(Request::get('date'))){
            $query->whereDate('created_at', '=', Request::get('date'));
        }

        // Apply pagination
        return $query->paginate(20);
    }
    static public function getEmailSingle($email)
    {
        return User::where('email', '=', $email)->first();
    }

    static public function getTokenSingle($remember_token)
    {
        return User::where('remember_token', '=', $remember_token)->first();
    }

}
