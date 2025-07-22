<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KnowledgeBase;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KnowledgeBaseResource\Pages;
use App\Filament\Resources\KnowledgeBaseResource\RelationManagers;
use Filament\Tables\Actions\DeleteAction;

class KnowledgeBaseResource extends Resource
{
    protected static ?string $model = KnowledgeBase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                ->label('Isi Pengetahuan')
                ->required()
                ->rows(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('content')
                ->label('Isi Konten')
                ->wrap() // Membungkus teks agar tidak terpotong satu baris
                ->html() // Jika kamu menyimpan HTML di field content
                ->limit(null), // Tampilkan konten secara penuh
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            DeleteAction::make(),
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
            'index' => Pages\ListKnowledgeBases::route('/'),
            'create' => Pages\CreateKnowledgeBase::route('/create'),
            'edit' => Pages\EditKnowledgeBase::route('/{record}/edit'),
        ];
    }
}
