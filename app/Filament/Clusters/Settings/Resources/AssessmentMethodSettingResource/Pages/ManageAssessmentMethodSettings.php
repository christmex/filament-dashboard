<?php

namespace App\Filament\Clusters\Settings\Resources\AssessmentMethodSettingResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use App\Models\AssessmentMethodSetting;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Clusters\Settings\Resources\AssessmentMethodSettingResource;

class ManageAssessmentMethodSettings extends ManageRecords
{
    protected static string $resource = AssessmentMethodSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('CreateMaster')
                ->color('success')
                ->action(function(){
                    $assessmentMethodSetting = Helper::getAssessmentMethodSettings();

                    foreach ($assessmentMethodSetting as $key => $value) {
                        AssessmentMethodSetting::firstOrCreate(['name'=>$value,'order'=>$key+1]);
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
