<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class CalculatorScreen extends Screen
{
    public $name = 'Calculator';
    public $description = 'A simple calculator';

    public function query(): array
    {
        return [];
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('expression')
                    ->title('Expression')
                    ->placeholder('Enter your expression'),

                Button::make('Calculate')
                    ->method('calculate')
                    ->icon('calculator'),
            ]),
            Layout::view('calculator-history'),
        ];
    }

    public function calculate()
    {
        $expression = request('expression');
        $result = eval("return $expression;");

        // Save result to history (you can use a database or session)
        session()->push('history', "$expression = $result");

        return redirect()->route('platform.calculator');
    }
}
