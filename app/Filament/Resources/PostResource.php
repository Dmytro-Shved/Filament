<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use League\CommonMark\Input\MarkdownInput;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Blog";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Create a New Post')->tabs([
                    Tab::make('Tab 1')
                        ->icon('heroicon-o-inbox')->iconPosition(IconPosition::Before)->badge('Hi')
                        ->schema([
                        TextInput::make('title')->required()->rules(['min:2', 'max:100']),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        Select::make('category_id')
                            ->required()
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable(),
                        ColorPicker::make('color')->required(),
                    ]),


                    Tab::make('Content')
                        ->icon('heroicon-o-inbox-stack')
                        ->schema([
                        MarkdownEditor::make('content')->required()->columnSpanFull(),
                    ]),
                    Tab::make('Meta')
                        ->icon('heroicon-o-photo')
                        ->schema([
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbnails')
                            ->columnSpanFull(),
                        TagsInput::make('tags')->required(),
                        Checkbox::make('published'),
                    ]),
                ])->columnSpanFull()->activeTab(3)->persistTabInQueryString(),
//                Section::make('Create a Post')
//                    ->description('create a new post over here')
//                    ->schema([
//                    TextInput::make('title')->required()->rules(['min:2', 'max:100']),
//                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
//                    Select::make('category_id')
//                        ->required()
//                        ->label('Category')
//                        ->relationship('category', 'name')
//                            ->searchable(),
//                    ColorPicker::make('color')->required(),
//                    MarkdownEditor::make('content')->required()->columnSpanFull(),
//                ])->columnSpan(2)->columns(2),
//                Group::make()->schema([
//                    Section::make('Image')
//                        ->collapsible()
//                        ->schema([
//
//                        // If we delete images - filament WILL NOT delete them from the disk
//                        FileUpload::make('thumbnail')->disk('public')->directory('thumbnails')
//                            ->columnSpanFull(),
//
//                    ])->columnSpan(1),
//
//                    Section::make('Meta')->schema([
//                        TagsInput::make('tags')->required(),
//                        Checkbox::make('published'),
//                    ]),
//                ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('thumbnail')
                    ->toggleable(),

                ColorColumn::make('color')
                    ->toggleable(),

                TextColumn::make('title')
                ->sortable()
                ->searchable()
                ->toggleable(),

                TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('category.name')
                    ->badge()
                    ->color(function (string $state) : string{
                        return match ($state) {
                            'PHP' => 'success', // GREEN
                            'Filament' => 'primary', // YELLOW
                            'Livewire' => 'info', // BLUE
                            'Laravel' => 'danger' // RED
                        };
                    })
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('tags'),
                CheckboxColumn::make('published'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])

            ->filters([
//                Filter::make('Published Posts')->query(function ($query){
//                    return $query->where('published', true);
//                }),
//                Filter::make('UnPublished Posts')->query(function ($query){
//                    return $query->where('published', false);
//                }),
                TernaryFilter::make('published'),

                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->multiple()
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            AuthorsRelationManager::class,
            CommentsRelationManager::class
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
