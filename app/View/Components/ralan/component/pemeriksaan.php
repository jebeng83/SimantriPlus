<?php

namespace App\View\Components\ralan\component;

use Illuminate\View\Component;

class pemeriksaan extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.component.pemeriksaan');
    }
}
