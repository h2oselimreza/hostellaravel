<?php

namespace App\Traits;

trait TracksUser
{
    protected static function bootTracksUser()
    {
        static::creating(function ($model) {
            $model->created_by = auth()->check()
                ? auth()->user()->user_id
                : 'system';

                $model->updated_by = auth()->check()
                ? auth()->user()->user_id
                : 'system';
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->check()
                ? auth()->user()->user_id
                : 'system';
        });
    }
}

?>