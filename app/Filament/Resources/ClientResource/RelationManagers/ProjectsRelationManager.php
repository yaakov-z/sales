<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Columns\TextColumn::make('title')
                    ->label(__('title'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Project $record): string => $record->client?->name)
                    ->tooltip(fn (Project $record): ?string => $record->description),
                Columns\TextColumn::make('date_range')
                    ->label(__('dateRange'))
                    ->state(fn (Project $record): string => Carbon::parse($record->start_at)
                        ->longAbsoluteDiffForHumans(Carbon::parse($record->due_at), 2)
                    )
                    ->description(fn (Project $record): string => Carbon::parse($record->start_at)
                        ->isoFormat('ll') . ' - ' . ($record->due_at ? Carbon::parse($record->due_at)->isoFormat('ll') : '∞')
                    ),
                Columns\TextColumn::make('scope')
                    ->label(__('scope'))
                    ->state(fn (Project $record): string => $record->scope_range)
                    ->description(fn (Project $record): string => $record->price_per_unit),
                Columns\TextColumn::make('progress')
                    ->label(__('progress'))
                    ->state(fn (Project $record): string => $record->hours_with_label)
                    ->description(fn (Project $record): string => $record->progress_percent),
                Columns\TextColumn::make('created_at')
                    ->label(__('createdAt'))
                    ->datetime('j. F Y, H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Columns\TextColumn::make('updated_at')
                    ->label(__('updatedAt'))
                    ->datetime('j. F Y, H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\Action::make('create')
                    ->icon('tabler-plus')
                    ->label(__('create'))
                    ->url(fn (): string => '/projects/create'),
            ])
            ->actions([
                Actions\Action::make('edit')
                    ->icon('tabler-edit')
                    ->label('')
                    ->url(fn (Project $obj): string => "/projects/$obj->id/edit/"),
                Actions\ReplicateAction::make()->icon('tabler-copy')->label(''),
                Actions\DeleteAction::make()->icon('tabler-trash')->label(''),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()->icon('tabler-trash'),
                ])
                ->icon('tabler-dots-vertical'),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans_choice('project', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('project', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('project', 2);
    }
}
