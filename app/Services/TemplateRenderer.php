<?php

namespace App\Services;

use Illuminate\Contracts\View\Factory as ViewFactory;

class TemplateRenderer
{
    public function __construct(private readonly ViewFactory $view)
    {
    }

    public function render(string $template, array $data = []): string
    {
        $viewName = str_contains($template, 'emails.') ? $template : "emails.{$template}";

        return $this->view->make($viewName, $data)->render();
    }
}
