<?php

namespace Songshenzong\Log;

use Illuminate\Database\Eloquent\Model;


class SongshenzongLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data',
        'time',
        'uri',
        'ip',
        'method',
    ];

    protected $casts = [
        'data' => 'array',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];


    /**
     * @param $key
     *
     * @return false|string
     */
    public function getTimeAttribute($key)
    {
        return date('m/d/y H:i:s:ms', $key);
    }

}
