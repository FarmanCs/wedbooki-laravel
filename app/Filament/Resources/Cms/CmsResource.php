<?php
//
//namespace App\Filament\Resources\Cms;
//
//use App\Filament\Resources\Cms\Pages\CreateCms;
//use App\Filament\Resources\Cms\Pages\EditCms;
//use App\Filament\Resources\Cms\Pages\ListCms;
//use App\Filament\Resources\Cms\Pages\ViewCms;
//use App\Filament\Resources\Cms\Schemas\CmsForm;
//use App\Filament\Resources\Cms\Schemas\CmsInfolist;
//use App\Filament\Resources\Cms\Tables\CmsTable;
//use App\Models\Cms\CmsSetting;
//use BackedEnum;
//use Filament\Resources\Resource;
//use Filament\Schemas\Schema;
//use Filament\Support\Icons\Heroicon;
//use Filament\Tables\Table;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\SoftDeletingScope;
//
//class CmsResource extends Resource
//{
////    protected static ?string $model = CmsSetting::class;
//
//    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
//
//    protected static ?int $navigationSort = 7;
//
//
//    public static function form(Schema $schema): Schema
//    {
//        return CmsForm::configure($schema);
//    }
//
//    public static function infolist(Schema $schema): Schema
//    {
//        return CmsInfolist::configure($schema);
//    }
//
//    public static function table(Table $table): Table
//    {
//        return CmsTable::configure($table);
//    }
//
//    public static function getRelations(): array
//    {
//        return [
//            //
//        ];
//    }
//
//    public static function getPages(): array
//    {
//        return [
//            'index' => ListCms::route('/'),
//            'create' => CreateCms::route('/create'),
//            'view' => ViewCms::route('/{record}'),
//            'edit' => EditCms::route('/{record}/edit'),
//        ];
//    }
//
//    public static function getRecordRouteBindingEloquentQuery(): Builder
//    {
//        return parent::getRecordRouteBindingEloquentQuery()
//            ->withoutGlobalScopes([
//                SoftDeletingScope::class,
//            ]);
//    }
//}
