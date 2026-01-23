<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    public function index()
    {
        $clients = ApiClient::orderBy('created_at', 'desc')->get();
        return view('admin.api-clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ApiClient::create([
            'name' => $request->name,
            'token' => ApiClient::generateToken(),
            'aes_key' => ApiClient::generateAesKey(),
        ]);

        return redirect()->back()->with('success', 'API Client created successfully.');
    }

    public function toggle(ApiClient $apiClient)
    {
        $apiClient->update([
            'is_active' => !$apiClient->is_active
        ]);

        return redirect()->back()->with('success', 'API Client status updated.');
    }

    public function destroy(ApiClient $apiClient)
    {
        $apiClient->delete();
        return redirect()->back()->with('success', 'API Client deleted successfully.');
    }
}
