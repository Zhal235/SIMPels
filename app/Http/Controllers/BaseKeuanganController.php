<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

abstract class BaseKeuanganController extends Controller
{
    /**
     * Model class yang akan digunakan oleh child controller
     */
    protected string $modelClass;
    
    /**
     * View prefix untuk child controller
     */
    protected string $viewPrefix;
    
    /**
     * Route prefix untuk child controller
     */
    protected string $routePrefix;
    
    /**
     * Nama entitas untuk pesan (singular)
     */
    protected string $entityName;
    
    /**
     * Nama entitas untuk pesan (plural)
     */
    protected string $entityNamePlural;

    /**
     * Get common validation rules
     */
    protected function getCommonValidationRules(): array
    {
        return [
            'is_active' => 'boolean',
            'deskripsi' => 'nullable|string'
        ];
    }

    /**
     * Apply common filters to query
     */
    protected function applyCommonFilters($query, Request $request)
    {
        // Filter berdasarkan status aktif
        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        // Apply search filter - akan di-override di child class
        if ($request->filled('search')) {
            $this->applySearchFilter($query, $request->search);
        }

        return $query;
    }

    /**
     * Apply search filter - harus di-implement di child class
     */
    abstract protected function applySearchFilter($query, string $search);

    /**
     * Get validation rules - harus di-implement di child class
     */
    abstract protected function getValidationRules($id = null): array;

    /**
     * Handle successful response (JSON or redirect based on request expectation)
     */
    protected function successResponse(Request $request, string $message, string $route = null, $data = null)
    {
        if ($request->expectsJson()) {
            $response = [
                'success' => true,
                'message' => $message
            ];

            if ($data !== null) {
                $response['data'] = $data;
            }

            return response()->json($response);
        }

        return redirect()->route($route ?? 'dashboard')->with('success', $message);
    }

    /**
     * Handle error JSON response
     */
    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

    /**
     * Handle successful JSON response (alias for successResponse)
     */
    protected function successJsonResponse(string $message, $data = null): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    /**
     * Handle JSON response for dual purposes (JSON or view)
     */
    protected function jsonResponse(Request $request, $data, string $viewName = null)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return view($viewName, compact('data'));
    }

    /**
     * Handle error redirect response
     */
    protected function errorRedirect(string $message, string $route = null): RedirectResponse
    {
        $route = $route ?? $this->routePrefix . '.index';
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Handle dual response (JSON or redirect based on request expectation)
     */
    protected function handleResponse(Request $request, bool $success, string $message, $data = null, string $redirectRoute = null)
    {
        if ($request->expectsJson()) {
            return $success ? 
                $this->successResponse($message, $data) : 
                $this->errorResponse($message);
        }

        return $success ? 
            $this->successRedirect($message, $redirectRoute) : 
            $this->errorRedirect($message, $redirectRoute);
    }

    /**
     * Get paginated results with common settings
     */
    protected function getPaginatedResults($query, int $perPage = 10)
    {
        return $query->paginate($perPage);
    }

    /**
     * Format validation errors for consistent response
     */
    protected function formatValidationErrors($errors): array
    {
        $formatted = [];
        foreach ($errors as $field => $messages) {
            $formatted[$field] = is_array($messages) ? $messages[0] : $messages;
        }
        return $formatted;
    }

    /**
     * Validate input with given rules
     */
    protected function validateInput(Request $request, array $rules): array
    {
        return $request->validate($rules);
    }

    /**
     * Check if entity can be deleted (override in child class if needed)
     */
    protected function canDelete($entity): bool
    {
        return true;
    }

    /**
     * Get deletion error message (override in child class if needed)
     */
    protected function getDeletionErrorMessage(): string
    {
        return "Tidak dapat menghapus {$this->entityName} karena masih digunakan oleh data lain.";
    }
}
