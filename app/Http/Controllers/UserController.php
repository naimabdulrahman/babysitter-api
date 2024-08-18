<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            // Retrieve only id, name, and email columns from the database
            $users = User::select('id', 'name', 'email')->get();

            // Return success response with user data
            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return $this->errorResponse('Failed to retrieve users', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            // Find the user by ID or fail
            $user = User::select('id', 'name', 'email')->findOrFail($id);

            // Return success response with user data
            return $this->successResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            // Return error response if user not found or any other issue
            return $this->errorResponse('User not found', 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:3'
            ]);
    
            // Insert the new user into the database
            $user = User::create([
                'name' => $validatedData['name'] ?? null,
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);
    
            // Check if the insertion was successful
            if ($user) {
                return $this->successResponse(
                    $validatedData, // Return the created user data
                    'User created successfully'
                );
            } else {
                // Optionally throw an exception if the insertion fails
                throw new \Exception('Failed to create user');
            }
        } catch (ValidationException $e) {
            // Handle validation exceptions
            return $this->errorResponse(
                'Validation failed: ' . $e->getMessage(),
                422 // Unprocessable Entity
            );
        } catch (\Exception $e) {
            // Handle general exceptions
            return $this->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500 // Internal Server Error
            );
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $id,
                // Add other fields as needed
            ]);

            // Update the user with the validated data
            $user->update($validatedData);

            return $this->successResponse(
                $user,
                'User updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            // Handle user not found
            return $this->errorResponse(
                'User not found',
                404
            );
        } catch (QueryException $e) {
            // Handle database query exceptions
            return $this->errorResponse(
                'Database error occurred: ' . $e->getMessage(),
                500
            );
        } catch (\Exception $e) {
            // Handle general exceptions
            return $this->errorResponse(
                'An unexpected error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Delete the user
            $user->delete();

            // Return success response
            return $this->successResponse(
                [],
                'User deleted successfully'
            );
        } catch (ModelNotFoundException $e) {
            // Return error response if user not found
            return $this->errorResponse('User not found', 404);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return $this->errorResponse('Failed to delete user', 500);
        }
    }
}