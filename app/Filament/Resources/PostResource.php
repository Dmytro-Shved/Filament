<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section takes the entire width
                Section::make('Create a Post')
                    ->description('create a new post over here')
                    ->schema([
                    TextInput::make('title')
//                        ->numeric()->minValue(2)->maxValue(15) for int
//                        ->minLength(2) its only for TextInput
//                        ->maxLength(10) its only for TextInput
                        ->rules(['min:2', 'max:10'])
                        ->in(['test', 'laravel'])
                        ->required(),

                    /**[ignoreRecord: true]
                    * But if you edit an existing post and do not change the slug field,
                    * then the uniqueness validation by default will assume that it is a duplicate
                    * and throw an error
                    */
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),

                    Select::make('category_id')
                        ->required()
                        ->label('Category')
                        ->options(Category::all()->pluck('name', 'id')),

                    ColorPicker::make('color')->required(),

                    MarkdownEditor::make('content')->required()->columnSpanFull(),
                ])->columnSpan(2)->columns(2),

                Group::make()->schema([
                    Section::make('Image')
                        ->collapsible()
                        ->schema([

                        // If we delete images - filament WILL NOT delete them from the disk
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbnails')
                            ->columnSpanFull(),

                    ])->columnSpan(1),

                    Section::make('Meta')->schema([
                        TagsInput::make('tags')->required(),
                        Checkbox::make('published'),
                    ]),
                ]),
            ])->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail'),
                ColorColumn::make('color'),
                TextColumn::make('title'),
                TextColumn::make('slug'),
                TextColumn::make('category.name'),
                TextColumn::make('tags'),
                CheckboxColumn::make('published'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
