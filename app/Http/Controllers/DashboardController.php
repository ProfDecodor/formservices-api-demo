<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $resources = [
            [
                'name'        => 'Applications',
                'description' => 'Browse and search available form applications.',
                'icon'        => 'bi-grid',
                'color'       => 'primary',
                'available'   => true,
                'route'       => route('applications.index'),
            ],
            [
                'name'        => 'Files',
                'description' => 'List, filter and paginate form files (datastores).',
                'icon'        => 'bi-file-earmark',
                'color'       => 'success',
                'available'   => true,
                'route'       => route('files.index'),
            ],
            [
                'name'        => 'Auth',
                'description' => 'Inspect the current authenticated user.',
                'icon'        => 'bi-person-circle',
                'color'       => 'info',
                'available'   => true,
                'route'       => route('auth.index'),
            ],
            [
                'name'        => 'File Creation',
                'description' => 'Start a new file from an application.',
                'icon'        => 'bi-file-earmark-plus',
                'color'       => 'warning',
                'available'   => false,
                'route'       => '#',
            ],
            [
                'name'        => 'Attachments',
                'description' => 'Manage attachments on a file.',
                'icon'        => 'bi-paperclip',
                'color'       => 'secondary',
                'available'   => false,
                'route'       => '#',
            ],
            [
                'name'        => 'Documents',
                'description' => 'View generated documents for a file.',
                'icon'        => 'bi-file-pdf',
                'color'       => 'danger',
                'available'   => false,
                'route'       => '#',
            ],
            [
                'name'        => 'Contents',
                'description' => 'Studio — manage form contents and versions.',
                'icon'        => 'bi-code-square',
                'color'       => 'primary',
                'available'   => false,
                'route'       => '#',
            ],
            [
                'name'        => 'Projects',
                'description' => 'Studio — deploy and test projects.',
                'icon'        => 'bi-kanban',
                'color'       => 'success',
                'available'   => true,
                'route'       => route('projects.index'),
            ],
        ];

        // Build client configuration status from config
        $clientsConfig = config('formservices-api-client.clients', []);
        $defaultClient = config('formservices-api-client.default', 'main');

        $clients = [];
        foreach ($clientsConfig as $name => $cfg) {
            $url = $cfg['base_url'] ?? '';
            $login = $cfg['login'] ?? '';
            $clients[$name] = [
                'url'        => $url,
                'login'      => $login,
                'version'    => $cfg['api_version'] ?? '',
                'configured' => ! empty($url) && ! empty($login),
            ];
        }

        $anyClientConfigured = collect($clients)->contains('configured', true);

        return view('dashboard', compact('resources', 'clients', 'defaultClient', 'anyClientConfigured'));
    }
}