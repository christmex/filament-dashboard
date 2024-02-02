<?php

namespace App\Filament\Clusters\Settings\Resources\TopicSettingResource\Pages;

use App\Filament\Clusters\Settings\Resources\TopicSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTopicSettings extends ManageRecords
{
    protected static string $resource = TopicSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
