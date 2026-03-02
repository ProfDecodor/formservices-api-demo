<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\View\View;
use Jway\FormServicesApiClient\Facades\FormServicesClient;

class AuthController extends Controller
{
    public function index(): View
    {
        $error = null;
        $me    = [];

        try {
            $me = FormServicesClient::api()->auth()->me();
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
        }

        // Build a display-safe copy: omit bulky keys (texts, applications)
        $meForDisplay = $me;
        if (isset($meForDisplay['texts'])) {
            $meForDisplay['texts'] = '(' . count($me['texts']) . ' entries — omitted for display)';
        }
        if (isset($meForDisplay['applications'])) {
            $meForDisplay['applications'] = '(' . count($me['applications']) . ' entries — omitted for display)';
        }

        $codeSnippet = <<<'PHP'
use Jway\FormServicesApiClient\Facades\FormServicesClient;

$api = FormServicesClient::api();

// Fetch authenticated user info and UI context
$me = $api->auth()->me();

// Response keys:
// - account        : user profile (id, name, fullName, email, status, connectionCount)
// - authorizations : array of permission strings
// - language       : current language code
// - languages      : available languages map
// - searchFields   : configured search fields
// - applications   : applications visible to this user
// - groups, services, texts (i18n)
PHP;

        return view('auth.index', compact('me', 'meForDisplay', 'error', 'codeSnippet'));
    }
}