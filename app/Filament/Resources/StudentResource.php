<?php

namespace App\Filament\Resources;

use App\Exports\StudentsExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Academic Management';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('class.name')
                    ->searchable()
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section.name')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

                Tables\Filters\Filter::make('has_class')
                    ->label('has class')
                    ->form([
                        Forms\Components\Select::make('class_id')
                            ->label('Filter By Class')
                            ->placeholder('Filter by Class')
                            ->live()
                            ->options(function () {
                                return Classes::pluck('name', 'id')->toArray();
                            }),
                        Forms\Components\Select::make('section_id')
                            ->label('Filter By Section')
                            ->placeholder('Filter by Section')
                            ->options(function (Get $get) {
                                return Section::where('class_id', $get('class_id'))->pluck('name', 'id')->toArray();
                            })
                    ])->query(function ($query, array $data) {
                        return $query->when($data['class_id'] ?? null, function ($query) use ($data) {
                            return $query->where('class_id', $data['class_id']);
                        })->when($data['section_id'] ?? null, function ($query) use ($data) {
                            return $query->where('section_id', $data['section_id']);
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_invoice')
                    ->url(fn(Student $student) => route('invoice.generate', $student))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export to Excel')
                        ->icon('heroicon-o-rectangle-stack')
                        ->action(function (Collection $records) {
                            return Excel::download(new StudentsExport($records), 'students.xlsx');
                        })
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('class_id')
                    ->live()
                    ->relationship('class', 'name'),
                Forms\Components\Select::make('section_id')
                    ->label("Section")
                    ->options(function (Get $get) {
                        $class_id = $get('class_id');
                        if ($class_id) {
                            return Section::where('class_id', $class_id)->pluck('name', 'id');
                        }
                    }),
                Forms\Components\TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->unique()
                    ->email()
                    ->required()
                    ->maxLength(255),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
