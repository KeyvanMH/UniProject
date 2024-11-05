<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DissertationDoctorResource\Pages;
use App\Filament\Resources\DissertationDoctorResource\RelationManagers;
use App\Http\Middleware\AdminMiddleware;
use App\Models\DissertationDoctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DissertationDoctorResource extends Resource
{
    protected static ?string $model = DissertationDoctor::class;
    protected static ?string $navigationGroup = 'مدیریت پایان نامه ها';

    protected static ?string $label = 'پایان نامه  دکتری';
    protected static ?string $pluralLabel = 'پایان نامه های دکتری';
    protected static string | array $routeMiddleware = [
        AdminMiddleware::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship(name: 'user',titleAttribute: 'name',modifyQueryUsing: function($query){
                        return $query->whereDoesntHave('dissertationDoctor');
                    })
                    ->searchable(['name'])
                    ->placeholder('نام هیئت علمی')
                    ->preload()
                    ->columnSpanFull(),

                Forms\Components\Placeholder::make('no label')
                    ->hiddenLabel()
                    ->content('هر پایان نامه را با "/" جدا کنید.')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('dissertation_1401')
                    ->label('پایان نامه های ۱۴۰۱')
                    ->formatStateUsing(function($state){
                        if(is_array($state)){
                            return implode('/',json_decode($state,true));
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
                Forms\Components\Textarea::make('dissertation_1402')
                    ->label('پایان نامه های ۱۴۰۲')
                    ->formatStateUsing(function($state){
                        if(is_array($state)){
                            return implode('/',json_decode($state,true));
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('نام هیئت علمی')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dissertation_1401')
                    ->label('پایان نامه های ۱۴۰۱')
                    ->formatStateUsing(function($state){
                        $array =  json_decode($state,true);
                        return implode("</br>",$array);
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('dissertation_1402')
                    ->label('پایان نامه های ۱۴۰۲')
                    ->formatStateUsing(function($state){
                        $array =  json_decode($state,true);
                        return implode("</br>",$array);
                    })
                    ->html(),
            ])
            ->recordUrl(null)
            ->filters([
                Tables\Filters\SelectFilter::make('user')->relationship('user','name')
                    ->label('هیئت علمی')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDissertationDoctors::route('/'),
            'create' => Pages\CreateDissertationDoctor::route('/create'),
            'edit' => Pages\EditDissertationDoctor::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool {
        return auth()->user()->role == 'admin';
    }

    public static function canView(Model $record): bool {
        return auth()->user()->role == 'admin';
    }
}
