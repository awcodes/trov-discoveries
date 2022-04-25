<?php

namespace App\Filament\Resources\Trov;

use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Trov\Forms\Components\Meta;
use Trov\Traits\HasSoftDeletes;
use Filament\Resources\Resource;
use TrovComponents\Enums\Status;
use App\Models\DiscoveryArticle;
use TrovComponents\Filament\Panel;
use Filament\Forms\Components\Group;
use TrovComponents\Forms\Timestamps;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use TrovComponents\Forms\TitleWithSlug;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Trov\Forms\Components\FeaturedImage;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use TrovComponents\Filament\FixedSidebar;
use Filament\Forms\Components\Placeholder;
use Trov\Tables\Columns\FeaturedImageColumn;
use Filament\Forms\Components\BelongsToSelect;
use FilamentCurator\Forms\Components\MediaPicker;
use Trov\Forms\Components\PageBuilder;
use TrovComponents\Tables\Columns\TitleWithStatus;
use TrovComponents\Tables\Filters\SoftDeleteFilter;
use Trov\Resources\RelationManagers\LinkSetsRelationManager;
use App\Filament\Resources\Trov\DiscoveryArticleResource\Pages\EditDiscoveryArticle;
use App\Filament\Resources\Trov\DiscoveryArticleResource\Pages\ListDiscoveryArticles;
use App\Filament\Resources\Trov\DiscoveryArticleResource\Pages\CreateDiscoveryArticle;

class DiscoveryArticleResource extends Resource
{
    use HasSoftDeletes;

    protected static ?string $model = DiscoveryArticle::class;

    protected static ?string $label = 'Article';

    protected static ?string $navigationLabel = 'Articles';

    protected static ?string $navigationGroup = 'Discovery Center';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Form $form): Form
    {
        return FixedSidebar::make()
            ->schema([
                TitleWithSlug::make('title', 'slug', '/discover/')->columnSpan('full'),
                FeaturedImage::make(),
                Section::make('Page Content')
                    ->schema([
                        PageBuilder::make('content')
                    ])
            ], [
                Panel::make('Details')
                    ->collapsible()
                    ->schema([
                        Select::make('status')
                            ->default('Draft')
                            ->options(Status::class)
                            ->required()
                            ->columnSpan(2),
                        DatePicker::make('published_at')
                            ->label('Publish Date')
                            ->withoutSeconds()
                            ->columnSpan(2),
                        BelongsToSelect::make('discovery_topic_id')
                            ->relationship('topic', 'title')
                            ->required()
                            ->columnSpan(2),
                        BelongsToSelect::make('author_id')
                            ->relationship('author', 'name')
                            ->required()
                            ->columnSpan(2),
                        Timestamps::make()
                    ]),
                Meta::make(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                FeaturedImageColumn::make('featured_image')->label('Thumb'),
                TitleWithStatus::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('topic.title')->searchable()->sortable(),
                BadgeColumn::make('meta.indexable')
                    ->label('SEO')
                    ->enum([
                        true => 'Index',
                        false => '—',
                    ])
                    ->colors([
                        'success' => true,
                        'secondary' => false,
                    ]),
                TextColumn::make('published_at')->label('Published At')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Status::class),
                SelectFilter::make('discovery_topic_id')->label('Topic')->relationship('topic', 'title'),
                SelectFilter::make('author_id')->label('Author')->relationship('author', 'name'),
                SoftDeleteFilter::make(),
            ])->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return array_merge([], config('trov.features.link_sets.active') ? [LinkSetsRelationManager::class] : []);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiscoveryArticles::route('/'),
            'create' => CreateDiscoveryArticle::route('/create'),
            'edit' => EditDiscoveryArticle::route('/{record}/edit'),
        ];
    }
}