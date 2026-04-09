<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Jway\FormServicesApiClient\Facades\FormServicesClient;

class ProjectController extends Controller
{
    /**
     * List all Studio projects via projects()->findAll().
     *
     * projects()->findAll() is equivalent to contents()->findAll('PROJECT')
     * but is preferred when the intent is specifically about projects.
     */
    public function index(Request $request): View
    {
        $error    = null;
        $projects = [];
        $search   = trim($request->query('search', ''));

        try {
            $api      = FormServicesClient::api();
            $projects = $api->projects()->findAll($search ?: null);
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        $codeSnippet = $this->buildIndexCodeSnippet($search);

        return view('projects.index', compact('projects', 'search', 'error', 'codeSnippet'));
    }

    /**
     * Show project details and its embedded file list.
     *
     * Uses contents()->find($contentId) which returns the full project record
     * including the 'files' array — the same approach used in Rosetta's
     * ImportProjects and ListProjects commands.
     *
     * The $id parameter is the contentId field from the project record,
     * not the legacy id. All content/project endpoints use contentId.
     */
    public function show(int $id): View
    {
        $error   = null;
        $project = [];
        $files   = [];

        try {
            $api     = FormServicesClient::api();
            $project = $api->contents()->find($id);
            $files   = $project['files'] ?? [];
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        $codeSnippet = $this->buildShowCodeSnippet($id);

        return view('projects.show', compact('project', 'files', 'id', 'error', 'codeSnippet'));
    }

    /**
     * Display the raw content of a specific project file.
     *
     * The REST endpoint GET rest/content/{id}/file/{fileId} returns the raw file
     * bytes, not JSON — so contents()->files()->find() cannot be used for metadata
     * (it would try to JSON-decode a raw response). Instead, we fetch the full
     * project via contents()->find() to get the embedded file list (same source
     * as the show page), locate the file by ID, then call getRaw() for the content.
     * This mirrors exactly what Rosetta's ImportProjects command does.
     */
    public function showFile(int $id, int $fileId): View
    {
        $error    = null;
        $file     = [];
        $content  = null;
        $isBinary = false;

        try {
            $api     = FormServicesClient::api();

            // Metadata: embedded in the project record (same call as show())
            $project = $api->contents()->find($id);
            $file    = collect($project['files'] ?? [])
                ->firstWhere('id', $fileId) ?? [];

            $fileType = $file['type'] ?? '';
            $filename = $file['filename'] ?? '';
            $isBinary = $this->isBinaryFile($fileType, $filename);

            if (! $isBinary) {
                // Raw content — getRaw() is required because the endpoint returns
                // plain text/XML, not JSON. Same technique as Rosetta ImportProjects.
                $content = FormServicesClient::client()->getRaw(
                    "rest/content/{$id}/file/{$fileId}"
                );
            }
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        $codeSnippet = $this->buildFileCodeSnippet($id, $fileId);

        return view('projects.file', compact(
            'file', 'content', 'isBinary', 'id', 'fileId', 'error', 'codeSnippet'
        ));
    }

    /**
     * Determine if a file is binary (not suitable for raw text display).
     *
     * Uses substring matching on the MIME type rather than exact match,
     * so API variants like 'application/x.jway.jform+xml' are all caught
     * by the 'xml' check regardless of minor naming differences.
     * Falls back to file extension when the MIME type is absent.
     */
    private function isBinaryFile(string $fileType, string $filename): bool
    {
        if ($fileType !== '') {
            return ! (
                str_contains($fileType, 'xml')  ||
                str_contains($fileType, 'text') ||
                str_contains($fileType, 'json') ||
                str_contains($fileType, 'properties')
            );
        }

        // No MIME type — fall back to extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return ! in_array($ext, ['xml', 'jxml', 'properties', 'txt', 'json', 'html', 'css', 'js']);
    }

    /**
     * Prepare a project for a new build (writes content to server filesystem).
     */
    public function prepare(int $id): RedirectResponse
    {
        try {
            $api = FormServicesClient::api();
            $api->projects()->prepareForBuild($id);
            session()->flash('success', "Project #{$id} prepared for build successfully.");
        } catch (GuzzleException $e) {
            session()->flash('error', "Prepare failed: " . $e->getMessage());
        }

        return redirect()->route('projects.show', $id);
    }

    /**
     * Deploy the WAR file built from this project.
     */
    public function deploy(int $id): RedirectResponse
    {
        try {
            $api = FormServicesClient::api();
            $api->projects()->deploy($id);
            session()->flash('success', "Project #{$id} deployed successfully.");
        } catch (GuzzleException $e) {
            session()->flash('error', "Deploy failed: " . $e->getMessage());
        }

        return redirect()->route('projects.show', $id);
    }

    /**
     * Redirect to the deployed form (test the live deployment).
     */
    public function test(int $id): RedirectResponse
    {
        try {
            $api    = FormServicesClient::api();
            $result = $api->projects()->test($id);
            $url    = $result['url'] ?? $result['redirect'] ?? null;

            if ($url) {
                return redirect()->away($url);
            }
        } catch (GuzzleException $e) {
            session()->flash('error', "Test failed: " . $e->getMessage());
        }

        return redirect()->route('projects.show', $id);
    }

    private function buildFileCodeSnippet(int $contentId, int $fileId): string
    {
        return <<<PHP
use Jway\FormServicesApiClient\Facades\FormServicesClient;

\$api = FormServicesClient::api();

// Step 1 — get file metadata from the project record.
// The endpoint GET rest/content/{id}/file/{fileId} returns the raw file bytes,
// not JSON, so contents()->files()->find() cannot be used for metadata.
// The file list is already embedded in contents()->find() — no extra request.
\$project = \$api->contents()->find({$contentId});
\$file    = collect(\$project['files'] ?? [])->firstWhere('id', {$fileId});

// Step 2 — fetch raw file content.
// getRaw() bypasses JSON decoding and returns the response body as a string.
// This is required for XML, JXML, properties files, etc.
// Same technique used in Rosetta's ImportProjects command.
\$content = FormServicesClient::client()->getRaw(
    'rest/content/{$contentId}/file/{$fileId}'
);
PHP;
    }

    private function buildIndexCodeSnippet(string $search): string
    {
        $lines = [
            'use Jway\FormServicesApiClient\Facades\FormServicesClient;',
            '',
            '$api = FormServicesClient::api();',
            '',
            '// List all Studio projects.',
            '// Equivalent to contents()->findAll(\'PROJECT\') but more explicit.',
            '$projects = $api->projects()->findAll();',
        ];

        if ($search !== '') {
            $lines[] = '';
            $lines[] = '// Filter by name (partial match):';
            $lines[] = "\$projects = \$api->projects()->findAll(name: '{$search}');";
        }

        $lines[] = '';
        $lines[] = '// Each project has both an id and a contentId field.';
        $lines[] = '// Always use contentId for build/deploy operations:';
        $lines[] = '// $contentId = $project[\'contentId\'];';

        return implode("\n", $lines);
    }

    private function buildShowCodeSnippet(int $contentId): string
    {
        return <<<PHP
use Jway\FormServicesApiClient\Facades\FormServicesClient;

\$api = FormServicesClient::api();

// Fetch full project details including the embedded files array.
// This is the primary way to inspect a project's content — same approach
// used in Rosetta's ImportProjects and ListProjects commands.
\$project = \$api->contents()->find({$contentId});

\$files = \$project['files'] ?? []; // array of file objects (filename, type, size, lastUpdate…)

// Alternatively, fetch files via the sub-resource:
\$files = \$api->contents()->files()->findAll({$contentId});

// Fetch a specific file's raw content (XML, properties, etc.):
\$fileId  = \$files[0]['id'];
\$content = \$api->contents()->files()->find({$contentId}, \$fileId);
PHP;
    }
}