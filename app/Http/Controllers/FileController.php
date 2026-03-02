<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Jway\FormServicesApiClient\Facades\FormServicesClient;

class FileController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request): View
    {
        $error      = null;
        $files      = [];
        $pagination = null;
        $applications = [];

        $page            = max(1, (int) $request->query('page', 1));
        $applicationName = trim($request->query('application', ''));
        $workflowStatus  = trim($request->query('status', ''));

        $filters = [
            'max'   => self::PAGE_SIZE,
            'first' => ($page - 1) * self::PAGE_SIZE + 1,
        ];

        if ($applicationName !== '') {
            $filters['application.name'] = $applicationName;
        }

        if ($workflowStatus !== '') {
            $filters['workflowStatus'] = $workflowStatus;
        }

        try {
            $api          = FormServicesClient::api();
            $applications = $api->applications()->findAll();
            $result       = $api->files()->findManagedWithHeaders($filters);

            $files      = $result['body'] ?? [];
            $pagination = $this->parsePagination($result['headers'] ?? [], $page);
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        // Sort applications alphabetically for the select
        usort($applications, fn ($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

        $codeSnippet = $this->buildCodeSnippet($filters, $page, $applicationName, $workflowStatus);

        return view('files.index', compact(
            'files', 'pagination', 'page',
            'applicationName', 'workflowStatus',
            'applications', 'error', 'codeSnippet'
        ));
    }

    public function show(string $uuid): View
    {
        $error = null;
        $file  = [];

        try {
            $api  = FormServicesClient::api();
            $file = $api->files()->find($uuid);
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        $documents = $file['documents'] ?? [];

        $codeSnippet = <<<PHP
use Jway\FormServicesApiClient\Facades\FormServicesClient;

\$api = FormServicesClient::api();

// Fetch file metadata by UUID (documents are embedded in the response)
\$file = \$api->files()->find('{$uuid}');

\$documents = \$file['documents'] ?? []; // array of document objects
PHP;

        return view('files.show', compact(
            'file', 'uuid', 'documents', 'error', 'codeSnippet'
        ));
    }

    private function parsePagination(array $headers, int $currentPage): array
    {
        // X-Content-Range: items {start}-{end}/{total}
        $rangeHeader = $headers['X-Content-Range'][0]
            ?? $headers['x-content-range'][0]
            ?? null;

        $total = 0;
        $start = 0;
        $end   = 0;

        if ($rangeHeader && preg_match('/(\d+)-(\d+)\/(\d+)/', $rangeHeader, $m)) {
            $start = (int) $m[1];
            $end   = (int) $m[2];
            $total = (int) $m[3];
        }

        $totalPages = ($total > 0) ? (int) ceil($total / self::PAGE_SIZE) : 1;

        return [
            'total'       => $total,
            'start'       => $start,
            'end'         => $end,
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'rawHeader'   => $rangeHeader,
        ];
    }

    private function buildCodeSnippet(
        array $filters,
        int $page,
        string $applicationName,
        string $workflowStatus
    ): string {
        $first = $filters['first'];

        $lines = [
            'use Jway\FormServicesApiClient\Facades\FormServicesClient;',
            '',
            '$api = FormServicesClient::api();',
            '',
            '// Pagination — API uses 1-based "first" + "max"',
            "\$page  = {$page};",
            "\$first = (\$page - 1) * " . self::PAGE_SIZE . " + 1; // = {$first}",
            '',
            '$filters = [',
            "    'max'   => " . self::PAGE_SIZE . ",",
            "    'first' => \$first,",
        ];

        if ($applicationName !== '') {
            $lines[] = "    'application.name' => '{$applicationName}',";
        }

        if ($workflowStatus !== '') {
            $lines[] = "    'workflowStatus'  => '{$workflowStatus}',";
        }

        $lines[] = '];';
        $lines[] = '';
        $lines[] = '// findManagedWithHeaders returns body + HTTP headers';
        $lines[] = '$result = $api->files()->findManagedWithHeaders($filters);';
        $lines[] = '';
        $lines[] = "\$files   = \$result['body'];    // array of file objects";
        $lines[] = "\$headers = \$result['headers']; // includes X-Content-Range";
        $lines[] = '';
        $lines[] = '// Parse total count from X-Content-Range header';
        $lines[] = '// e.g. "1-20/15034" → 15034 total, showing items 1→20';
        $lines[] = "preg_match('/(\\d+)-(\\d+)\\/(\\d+)/', \$headers['X-Content-Range'][0], \$m);";
        $lines[] = '$total      = (int) $m[3];';
        $lines[] = '$totalPages = (int) ceil($total / ' . self::PAGE_SIZE . ');';

        return implode("\n", $lines);
    }
}
