<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

class DateQueryHelper
{
    public static function yearExpression(string $column, ?string $tableAlias = null): string
    {
        $qualifiedColumn = $tableAlias ? sprintf('%s.%s', $tableAlias, $column) : $column;

        return match (DB::getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', {$qualifiedColumn}) AS INTEGER)",
            'pgsql' => "EXTRACT(YEAR FROM {$qualifiedColumn})",
            default => "YEAR({$qualifiedColumn})",
        };
    }
}
