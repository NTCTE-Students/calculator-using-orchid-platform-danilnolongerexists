<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ConverterScreen extends Screen
{
    public $name = 'Unit Converter';
    public $description = 'Convert between different units';

    protected $units = [
        'length' => [
            'meters' => 1,
            'kilometers' => 0.001,
            'miles' => 0.000621371,
        ],
        'mass' => [
            'grams' => 1,
            'kilograms' => 0.001,
            'pounds' => 0.00220462,
        ],
        // Add other unit types here
    ];

    public function query(Request $request): array
    {
        $type = $request->get('type', 'length'); // Default to 'length' if not set
        return [
            'unitTypes' => $this->getUnitTypes(),
            'units' => $this->getUnits($type),
            'selectedType' => $type,
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Select::make('type')
                    ->options($this->getUnitTypes())
                    ->title('Type of Measurement')
                    ->required()
                    ->value(request('type', 'length')) // Default to 'length' if not set
                    ->onChange('updateUnits'),

                Select::make('from_unit')
                    ->options($this->getUnits(request('type', 'length')))
                    ->title('From Unit')
                    ->required(),

                Input::make('value')
                    ->title('Value')
                    ->type('number')
                    ->required(),

                Select::make('to_unit')
                    ->options($this->getUnits(request('type', 'length')))
                    ->title('To Unit')
                    ->required(),

                Button::make('Convert')
                    ->method('convert')
                    ->icon('refresh'),
            ]),
            Layout::view('converter-result'),
        ];
    }

    public function convert(Request $request)
    {
        $type = $request->input('type');
        $from = $request->input('from_unit');
        $to = $request->input('to_unit');
        $value = $request->input('value');

        if (!isset($this->units[$type][$from]) || !isset($this->units[$type][$to])) {
            session()->flash('result', 'Invalid unit type or unit');
            return redirect()->route('platform.converter');
        }

        $result = $value * ($this->units[$type][$to] / $this->units[$type][$from]);

        session()->flash('result', "$value $from is equal to $result $to");

        return redirect()->route('platform.converter', ['type' => $type]);
    }

    private function getUnitTypes()
    {
        $types = [];
        foreach ($this->units as $type => $units) {
            $types[$type] = ucfirst($type);
        }
        return $types;
    }

    private function getUnits($type)
    {
        $units = [];
        foreach ($this->units[$type] as $unit => $value) {
            $units[$unit] = ucfirst($unit);
        }
        return $units;
    }
}
