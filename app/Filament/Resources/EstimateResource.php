<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstimateResource\Pages;
use App\Filament\Resources\EstimateResource\RelationManagers;
use App\Models\Estimate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;
    protected static ?string $navigationIcon = 'tabler-clock-code';
    protected static ?int $navigationSort = 25;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('project_id')
                    ->translateLabel()
                    ->relationship('project', 'title')
                    ->native(false)
                    ->searchable()
                    ->suffixIcon('tabler-package')
                    ->required(),
                TextInput::make('title')
                    ->translateLabel()
                    ->required(),
                Textarea::make('description')
                    ->translateLabel()
                    ->autosize()
                    ->maxLength(65535),
                TextInput::make('amount')
                    ->translateLabel()
                    ->numeric()
                    ->step(0.1)
                    ->minValue(0.1)
                    ->suffixIcon('tabler-clock-exclamation'),
                TextInput::make('weight')
                    ->translateLabel()
                    ->numeric()
                    ->step(1)
                    ->suffixIcon('tabler-arrows-sort'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('project.title')
            ->columns([
                ColorColumn::make('project.client.color')
                    ->label('')
                    ->tooltip(fn (Estimate $record): string => $record->project?->client?->name),
                TextColumn::make('title')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->description(fn (Estimate $record): string => substr($record->description, 0, 75) . '...'),
                TextColumn::make('amount')
                    ->label(__('Hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->translateLabel()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->translateLabel()
                    ->datetime('j. F Y, H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->translateLabel()
                    ->datetime('j. F Y, H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions(
                ActionGroup::make([
                    EditAction::make()->icon('tabler-edit'),
                    ReplicateAction::make()->icon('tabler-copy'),
                    DeleteAction::make()->icon('tabler-trash'),
                ])
                ->icon('tabler-dots-vertical')
            )
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->icon('tabler-trash'),
                ])
                ->icon('tabler-dots-vertical'),
            ])
            ->emptyStateActions([
                CreateAction::make()->icon('tabler-plus'),
            ])
            ->emptyStateIcon('tabler-ban')
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEstimates::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Core data');
    }

    public static function getNavigationLabel(): string
    {
        return __('Estimates');
    }

    public static function getModelLabel(): string
    {
        return __('Estimate');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Estimates');
    }
}
