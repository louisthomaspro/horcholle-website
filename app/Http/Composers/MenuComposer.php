<?php

namespace App\Http\Composers;

use Illuminate\View\View;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class MenuComposer
{

    public function compose(View $view)
    {
        $categories_dd = DB::select('select id, filename from realisations where hierarchy = 1');
        $view->with('categories_dd', $categories_dd);
    }

}