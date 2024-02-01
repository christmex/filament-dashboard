<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DatePicker;

class EditUserStatus extends EditRecord
{
    protected static string $resource = UserResource::class;
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('bpjs_join_date'),
                        DatePicker::make('jht_join_date'),
                        DatePicker::make('kemnaker_join_date'),
                        DatePicker::make('read_employee_terms_date'),
                        Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }
}
