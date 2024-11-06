<?php

namespace Hybridly\Tests\Laravel\Tables\Fixtures;

use Hybridly\Refining\Filters\SelectFilter;
use Hybridly\Tables\Columns\TextColumn;
use Hybridly\Tables\Table;
use Hybridly\Tests\Fixtures\Database\Product;
use Hybridly\Tests\Fixtures\Vendor;

class BasicProductsTableWithFilterOptions extends Table
{
    protected string $model = Product::class;

    public function defineRefiners(): array
    {
        return [
            SelectFilter::make('os', alias: 'phone', options: [
                'iphone' => 'ios',
                'ipad' => 'ipados',
                'samsung' => 'android',
            ]),
            SelectFilter::make('enum', options: Vendor::class),
        ];
    }

    public function defineColumns(): array
    {
        return [
            TextColumn::make('os'),
            TextColumn::make('enum'),
        ];
    }
}
