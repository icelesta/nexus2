<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdmLimitMasters;

use App\Filament\Resources\AdmLimitMasters\Pages\CreateAdmLimitMaster;
use App\Filament\Resources\AdmLimitMasters\Pages\EditAdmLimitMaster;
use App\Filament\Resources\AdmLimitMasters\Pages\ListAdmLimitMasters;
use App\Filament\Resources\AdmLimitMasters\Schemas\AdmLimitMasterForm;
use App\Filament\Resources\AdmLimitMasters\Tables\AdmLimitMastersTable;
use App\Models\AdmLimitMaster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdmLimitMasterResource extends Resource
{
    protected static ?string $model = AdmLimitMaster::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedShieldExclamation;

    protected static ?string $navigationLabel =
        'ADM Limit Master';

    protected static ?string $modelLabel =
        'ADM Limit Master';

    protected static ?string $pluralModelLabel =
        'ADM Limit Masters';

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute =
        'description';

    public static function form(Schema $schema): Schema
    {
        return AdmLimitMasterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmLimitMastersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAdmLimitMasters::route('/'),
            'create' => CreateAdmLimitMaster::route('/create'),
            'edit'   => EditAdmLimitMaster::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()
            ->where('is_active', true)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::query()
            ->where('is_active', true)
            ->count();

        return match (true) {
            $count === 0 => 'danger',
            $count === 1 => 'success',
            default => 'warning',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active ADM Limit Configuration';
    }
}