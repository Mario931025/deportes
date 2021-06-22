<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class LayoutsComposer
{
    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        /*
        $adminCheck = false;
        
        if (request()->user()) {
            $roles = request()->user()->roles;
            $contains = $roles->whereIn('id', [3, 4, 5]);
            if ($contains->count() > 0) {
                $adminCheck = true;
            }
        }
        
        $view->with('adminCheck', $adminCheck);
        */
    }
}