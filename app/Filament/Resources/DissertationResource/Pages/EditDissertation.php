<?php

namespace App\Filament\Resources\DissertationResource\Pages;

use App\Filament\Resources\DissertationResource;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditDissertation extends EditRecord
{
    protected static string $resource = DissertationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function form(Form $form): Form {
        return $form
            ->schema([
                Placeholder::make('no label')
                    ->hiddenLabel()
                    ->content('هر پایان نامه را با  "/" جدا کنید.')
                    ->columnSpanFull(),
                Textarea::make('dissertation_1401')
                    ->label('پایان نامه های ۱۴۰۱')
                    ->formatStateUsing(function($state){
                        if(is_array($state) or is_array(json_decode($state,true))) {
                            return implode('/', json_decode($state, true));
                        }else{
                            return $state;
                        }
                    })
                    ->dehydrateStateUsing(function($state){
                        if(is_array($state)){
                            return json_encode($state);
                        }else{
                            return json_encode(explode("/",trim($state)));
                        }
                    })
                ,
                Textarea::make('dissertation_1402')
                    ->label('پایان نامه های ۱۴۰۲')
                    ->formatStateUsing(function($state){
                        if(is_array($state) or is_array(json_decode($state,true))) {
                            return implode('/', json_decode($state, true));
                        }else{
                            return $state;
                        }
                    })
                    ->dehydrateStateUsing(function($state){
                        if(is_array($state)){
                            return json_encode($state);
                        }else{
                            return json_encode(explode("/",trim($state)));
                        }
                    })
                ,
            ]);
    }
}
