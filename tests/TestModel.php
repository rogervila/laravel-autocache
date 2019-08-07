<?php

namespace Tests;

class TestModel extends \Illuminate\Database\Eloquent\Model
{
    use \LaravelAutoCache\Autocache;

    protected $fillable = ['id', 'dummy', 'othercolumn'];
}
