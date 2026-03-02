<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Jway\FormServicesApiClient\Facades\FormServicesClient;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $error = null;
        $applications = [];

        try {
            $api = FormServicesClient::api();
            $applications = $api->applications()->findAll();
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        // Collect all unique tags across all applications
        $allTags = [];
        foreach ($applications as $app) {
            foreach ($app['tags'] ?? [] as $tag) {
                $allTags[$tag['name']] = $tag['name'];
            }
        }
        ksort($allTags);

        // Apply filters from query string
        $search     = $request->query('search', '');
        $tagFilter  = $request->query('tag', '');
        $showHidden = $request->boolean('hidden', true);

        $filtered = array_values(array_filter($applications, function ($app) use ($search, $tagFilter, $showHidden) {
            // Visibility filter
            if (! $showHidden && ($app['hidden'] ?? false)) {
                return false;
            }

            // Search filter (by technical name)
            if ($search !== '' && stripos($app['name'], $search) === false) {
                return false;
            }

            // Tag filter
            if ($tagFilter !== '') {
                $tagNames = array_column($app['tags'] ?? [], 'name');
                if (! in_array($tagFilter, $tagNames, true)) {
                    return false;
                }
            }

            return true;
        }));

        // Stats
        $stats = [
            'total'   => count($applications),
            'visible' => count(array_filter($applications, fn ($a) => ! ($a['hidden'] ?? false))),
            'hidden'  => count(array_filter($applications, fn ($a) => $a['hidden'] ?? false)),
            'tags'    => count($allTags),
        ];

        // Build the code snippet reflecting the actual call made
        $codeSnippet = $this->buildIndexCodeSnippet($search, $tagFilter, $showHidden);

        return view('applications.index', compact(
            'filtered', 'stats', 'allTags',
            'search', 'tagFilter', 'showHidden', 'error', 'codeSnippet'
        ));
    }

    public function show(int $id): View
    {
        $error = null;
        $application = [];
        $metadata = [];

        try {
            $api = FormServicesClient::api();
            $application = $api->applications()->find($id);
            $metadata    = $api->applications()->getMetadata($id);
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        $codeSnippet = <<<PHP
use Jway\FormServicesApiClient\Facades\FormServicesClient;

\$api = FormServicesClient::api();

// Fetch the application by ID
\$application = \$api->applications()->find({$id});

// Fetch its datastores metadata
\$metadata = \$api->applications()->getMetadata({$id});
PHP;

        return view('applications.show', compact('application', 'metadata', 'id', 'error', 'codeSnippet'));
    }

    private function buildIndexCodeSnippet(string $search, string $tagFilter, bool $showHidden): string
    {
        $lines = [
            'use Jway\FormServicesApiClient\Facades\FormServicesClient;',
            '',
            '$api = FormServicesClient::api();',
            '',
            '// Fetch all applications (returns full list in one call)',
            '$applications = $api->applications()->findAll();',
        ];

        // Show client-side filtering applied
        $hasFilter = $search !== '' || $tagFilter !== '' || ! $showHidden;
        if ($hasFilter) {
            $lines[] = '';
            $lines[] = '// Client-side filters applied:';
        }

        if (! $showHidden) {
            $lines[] = '$applications = $api->applications()->findVisible();';
            $lines[] = '// equivalent to:';
            $lines[] = '// array_filter($applications, fn($app) => !($app[\'hidden\'] ?? false))';
        }

        if ($tagFilter !== '') {
            $lines[] = sprintf('$applications = $api->applications()->findByTag(\'%s\');', addslashes($tagFilter));
        }

        if ($search !== '') {
            $lines[] = sprintf('$applications = $api->applications()->findByName(\'%s\');', addslashes($search));
        }

        return implode("\n", $lines);
    }
}