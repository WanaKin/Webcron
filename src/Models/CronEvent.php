<?php

namespace WanaKin\Webcron\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @method static CronEvent create(array $data)
 * @property int $id
 * @property string $job
 * @property Carbon $dispatched_at
 */
class CronEvent extends Model
{
    public $timestamps = false;

    protected $casts = [
        'dispatched_at' => 'datetime'
    ];

    protected $fillable = [
        'job',
        'dispatched_at'
    ];
}