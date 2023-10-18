<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Requests\Connection\ConnectionFilterRequest;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Connection extends Model
{
    use HasFactory;
    const STATUS_ACTIVE = true;
    const RELATIONS = ["users"];

    protected $fillable = [
        'name',
        'host',
        'username',
        'password',
        'port',
        'database',
        'description',
        'status'
    ];

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'connection_user');
    }

    /**
     * @author Kareem Lorenzana
     * @created 2023-06-01
     * @params App\Http\Requests\Connection\ConnectionFilterRequest
     * @return Illuminate\Database\Eloquent\Builder
     * Filter data for connections list
     */
     public function filterData(ConnectionFilterRequest $request){
        $connections = $this->with(self::RELATIONS);
        $filter = $request->filter;
       if($request->has('filter')){

        $connections = $connections->where(function($query) use ($filter) {
            return $query->where('name', 'like', '%'.trim($filter).'%')
                            ->orWhere('host', 'like', '%'.trim($filter).'%')
                            ->orWhere('username', 'like', '%'.trim($filter).'%')
                            ->orWhere('password', 'like', '%'.trim($filter).'%')
                            ->orWhere('port', 'like', '%'.trim($filter).'%')
                            ->orWhere('database', 'like', '%'.trim($filter).'%')
                            ->orWhere('description', 'like', '%'.trim($filter).'%');
        });
       }

       if($request->has('sortBy') && in_array($request->sortBy, ['id', ...$this->fillable]) && $request->has('descending')){
        $orderBy = $request->descending == true? "desc": "asc";
        $sortBy = $request->sortBy;
        $connections = $connections->orderBy($sortBy, $orderBy);
        return $connections;
       }else{
            if($request->has('sortBy')){
                $orderBy = $request->descending == true? "desc": "asc";
                $sortBy = $request->sortBy;
                //order by relation in case the current model has to
                switch ($request->sortBy) {
                    case 'seller':
                        $connections = $connections->orderBy(Seller::select('first_name')
                            ->whereColumn('sellers.id', 'quotes.seller_id'), $orderBy);
                        break;
                    default:
                        # code...
                        break;
                }

                return $connections;
            }else{
                return $connections;
            }
       }

    }
    /**
     * @author Kareem Lorenzana
     * @created 2023-06-05
     * @params Illuminate\Database\Eloquent\Builder $query
     * @return Illuminate\Database\Eloquent\Builder
     * Scope a query to only include active connections.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }
}
