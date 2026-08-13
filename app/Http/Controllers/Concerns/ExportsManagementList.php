<?php

namespace App\Http\Controllers\Concerns;

use App\Support\ListExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ExportsManagementList
{
    protected function exportQueryParams(Request $request): array
    {
        return array_filter([
            'search' => trim($request->input('search', '')),
            'filter' => trim($request->input('filter', '')),
        ], fn ($value) => $value !== '');
    }

    /**
     * @param  list<string>  $headers
     * @param  iterable<int, list<string|int|float|null>>  $rows
     */
    protected function exportCsvResponse(array $headers, iterable $rows, string $basename)
    {
        return ListExporter::csv(
            $headers,
            $rows,
            $basename . '-' . now()->format('Y-m-d') . '.csv'
        );
    }

    /**
     * @param  list<string>  $headers
     * @param  iterable<int, list<string|int|float|null>>  $rows
     */
    protected function exportPdfResponse(
        string $title,
        array $headers,
        iterable $rows,
        string $basename,
        ?string $subtitle = null,
    ) {
        return ListExporter::pdf(
            $title,
            $headers,
            $rows,
            $basename . '-' . now()->format('Y-m-d') . '.pdf',
            $subtitle
        );
    }

    protected function mapRowsFromQuery(Builder $query, callable $mapper): array
    {
        return $query->get()->map($mapper)->all();
    }
}
