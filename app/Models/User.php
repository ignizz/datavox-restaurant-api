<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Http\Requests\User\UserFilterRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @author Kareem Lorenzana
     * @create 2023-05-19
     * Constants for user model
     * @var array<uknown>, string
     */
    const USER_WRITER = 'capturista', USER_ADMIN='admin';
    const STATUS_ACTIVE = true, STATUS_INACTIVE = false;
    const PRINCIPAL_USER = 1; //usuario inmutable

    const USER_TYPES = [
        self::USER_WRITER,
        self::USER_ADMIN
    ];
    const RELATIONS = ["connections"];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'user_type',
        'status'
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
        'password' => 'hashed',
    ];

    /**
     * @author Kareem Lorenzana
     * @created 2023/06/07
     * The connections that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function connections():BelongsToMany
    {
        return $this->belongsToMany(Connection::class, 'connection_user', 'user_id', 'connection_id');
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-05-18
     * @params App\Http\Requests\UserFilterRequest
     * @return Illuminate\Database\Eloquent\Builder
     * Filter data for users list
     */
     public function filterData(UserFilterRequest $request){
        $users = $this->with(self::RELATIONS);
        $filter = $request->filter;
       if($request->has('filter')){

        $users = $users->where(function($query)use($filter){
            return $query->where('username', 'like', '%'.trim($filter).'%')
                    ->orWhere('email','like', '%'.trim($filter).'%')
                    ->orWhere('first_name','like', '%'.trim($filter).'%')
                    ->orWhere('last_name','like', '%'.trim($filter).'%')
                    ->orWhere('user_type','like', '%'.trim($filter).'%');
        });
       }

       if($request->has('sortBy') && in_array($request->sortBy, ['id', ...$this->fillable]) && $request->has('descending')){
        $orderBy = $request->descending == true? "desc": "asc";
        $sortBy = $request->sortBy;
        $users = $users->orderBy($sortBy, $orderBy);
        return $users;
       }else{
            if($request->has('sortBy')){
                $orderBy = $request->descending == true? "desc": "asc";
                $sortBy = $request->sortBy;
                //order by relation in case the current model has to
                switch ($request->sortBy) {
                    case 'seller':
                        $users = $users->orderBy(Seller::select('first_name')
                            ->whereColumn('sellers.id', 'quotes.seller_id'), $orderBy);
                        break;
                    default:
                        # code...
                        break;
                }

                return $users;
            }else{
                return $users;
            }
       }

    }

}
