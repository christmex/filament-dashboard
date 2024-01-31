<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use App\Models\Company;
use Filament\Notifications\Notification;
use App\Filament\Resources\CompanyResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCompanies extends ManageRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('CreateMaster')
                ->color('success')
                ->action(function(){
                    $companies = Helper::getCompanies();

                    foreach ($companies as $value) {
                        Company::firstOrCreate(['name'=>$value]);
                    }
                    Notification::make()
                        ->success()
                        ->title('yeayy, success!')
                        ->body('Successfully create master data')
                        ->send();
                })
                ,
        ];
    }
}
