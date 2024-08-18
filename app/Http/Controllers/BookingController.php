<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $bookings = Booking::select('id', 'fullname', 'email', 'phone_number', 'address', 'reservation_datetime', 'kid_detail', 'created_at')->get();

            // Return success response with booking data
            return $this->successResponse($bookings, 'Bookings retrieved successfully');
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return $this->errorResponse('Failed to retrieve bookings', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'fullname' => 'required|string|max:255',
                'email' => 'required|email',
                'phone_number' => 'required|string|min:3|max:15',
                'address' => 'required|string',
                'reservation_datetime' => 'required|date_format:Y-m-d H:i:s',
                'kid_detail' => 'required|array',
                'kid_detail.*.fullName' => 'required|string|max:255',
                'kid_detail.*.birthday' => 'required|date_format:Y-m-d H:i:s',
            ]);
    
            // Insert the new booking into the database
            $booking = Booking::create($validatedData);
    
            // Check if the insertion was successful
            if ($booking) {
                return $this->successResponse(
                    $validatedData, // Return the created booking data
                    'Booking created successfully'
                );
            } else {
                // Optionally throw an exception if the insertion fails
                throw new \Exception('Failed to create booking');
            }
        } catch (ValidationException $e) {
            
            // Handle validation exceptions
            return $this->errorResponse(
                'Validation failed: ' . $e->getMessage(),
                422 // Unprocessable Entity
            );
        } catch (QueryException $e) {
            // Handle query exceptions
            if ($e->getCode() === '23000') { // Integrity constraint violation
                return $this->errorResponse(
                    'Duplicate record found.',
                    409 // Conflict
                );
            }
    
            // Handle other SQL exceptions or rethrow
            return $this->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500 // Internal Server Error
            );
        } catch (\Exception $e) {
            // Handle general exceptions
            return $this->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500 // Internal Server Error
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the booking by ID or fail
            $booking = Booking::select('id', 'fullname', 'email', 'phone_number', 'address', 'reservation_datetime', 'kid_detail', 'created_at')->findOrFail($id);

            // Return success response with booking data
            return $this->successResponse($booking, 'Booking retrieved successfully');
        } catch (\Exception $e) {
            // Return error response if booking not found or any other issue
            return $this->errorResponse('Booking not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Find the booking by ID
            $booking = Booking::findOrFail($id);

            // Validate the request data
            $validatedData = $request->validate([
                'fullname' => 'nullable|string|max:255',
                'email' => 'nullable|email',
                'phone_number' => 'nullable|string|min:3|max:15',
                'address' => 'nullable|string',
                'reservation_datetime' => 'nullable|date_format:Y-m-d H:i:s',
                'kid_detail' => 'nullable|json'
            ]);

            // Update the booking with the validated data
            $booking->update($validatedData);

            return $this->successResponse(
                $booking,
                'Booking updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            // Handle booking not found
            return $this->errorResponse(
                'Booking not found',
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the booking by ID
            $booking = Booking::findOrFail($id);

            // Delete the booking
            $booking->delete();

            // Return success response
            return $this->successResponse(
                [],
                'Booking deleted successfully'
            );
        } catch (ModelNotFoundException $e) {
            // Return error response if booking not found
            return $this->errorResponse('Booking not found', 404);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return $this->errorResponse('Failed to delete booking', 500);
        }
    }
}
