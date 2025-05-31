<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FooterMegaMenu extends Component
{
    public array $sections;
    public array $settings;
    public array $contactInfo;

    public function __construct()
    {
        $config = config('footer_menu');
        
        $this->sections = collect($config['sections'])
            ->sortBy('order')
            ->toArray();
            
        $this->settings = $config['settings'];
        $this->contactInfo = $config['contact_info'];
    }

    public function render(): View
    {
        return view('components.footer-mega-menu');
    }
}