<?php

namespace Songshenzong\Log\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Songshenzong\Log\SongshenzongLog;

class WebController extends BaseController
{

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    public $app;


    /**
     * CurrentController constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this -> app = $app;

    }


    /**
     * Index
     */
    public function index()
    {
        $file = __DIR__ . '/../Views/index.html';
        echo file_get_contents($file);
        exit;
    }


}
