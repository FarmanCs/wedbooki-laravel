<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Admin\AdminTwoFactorCode;
use App\Models\SubCategory;
use App\Models\Vendor\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class AdminController extends Controller
{
    // Admin SignUp
    public function signup(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $first_name = $request->input('first_name');
            $email = strtolower($request->input('email')); // lowercase email
            $password = $request->input('password');

            // Check if admin already exists by email or phone_no
            $existAdmin = Admin::where('email', $email)->first();

            if ($existAdmin) {
                return response()->json([
                    'message' => 'Admin with this email or phone number already exists.'
                ], 400);
            }

            // Hash password
            $hashedPassword = Hash::make($password);

            // Create admin
            $newAdmin = Admin::create([
                'first_name' => $first_name,
                'email' => $email,
                'password' => $hashedPassword,
            ]);

            return response()->json(['message' => 'Admin created successfully'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Please try again later.'], 500);
        }
    }

    // Admin Login
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $email = strtolower($request->input('email'));
            $password = $request->input('password');

            // Check if admin exists
            $admin = Admin::where('email', $email)->first();
            if (!$admin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }

            // Check password
            if (!Hash::check($password, $admin->password)) {
                return response()->json(['message' => 'Password not matched'], 400);
            }

            // Generate 2FA code
            $code = rand(100000, 999999); // 6-digit code
            $expires = Carbon::now()->addMinutes(5);

            // Save 2FA code and expiration
            $admin->two_factor_code = $code;
            $admin->two_factor_code_expires = $expires;
            $admin->save();

            // Send email (adjust this to your Mailable)
            Mail::to($admin->email)->send(new AdminTwoFactorCode($admin->first_name, $code));

            return response()->json([
                'message' => 'Code for authentication sent to your email'
            ], 200);

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Please try again later.'], 500);
        }
    }

    // Verify 2FA
    public function verify2fa(Request $request)
    {
        try {
            // Validate email + code
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'code' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Email and 2FA code are required',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find admin
            $admin = Admin::where('email', strtolower($request->email))->first();

            if (!$admin || !$admin->two_factor_code || !$admin->two_factor_code_expires) {
                return response()->json([
                    'message' => 'Invalid request'
                ], 400);
            }

            // Check if expired
            $isExpired = now()->greaterThan($admin->two_factor_code_expires);

            // Match code + expiry
            if ($admin->two_factor_code != $request->code || $isExpired) {
                return response()->json([
                    'message' => 'Invalid or expired 2FA code'
                ], 400);
            }

            // Clear fields
            $admin->update([
                'two_factor_code' => null,
                'two_factor_code_expires' => null,
            ]);

            // Create token (Sanctum)
            $token = $admin->createToken('AdminAccessToken')->plainTextToken;

            return response()->json([
                'message' => '2FA verification successful',
                'AdminAccessToken' => $token
            ], 200);

        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Please try again later',
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    // Get All Hosts
    public function getAllHosts()
    {
        // Fetch hosts
        return response()->json(['hosts' => []]);
    }

    // Get All Vendors
    public function getAllVendors()
    {
        return response()->json(['vendors' => []]);
    }

    // Get All Bookings
    public function getAllBookings()
    {
        return response()->json(['bookings' => []]);
    }

    // Update Host Status
    public function updateHostStatus(Request $request, $id)
    {
        return response()->json(['message' => "Host $id status updated"]);
    }

    // Update Vendor Status
    public function updateVendorStatus(Request $request, $vendorId)
    {
        return response()->json(['message' => "Vendor $vendorId status updated"]);
    }

    // Get Host By ID
    public function getHostById($id)
    {
        return response()->json(['host' => ['id' => $id]]);
    }

    // Get Vendor By ID
    public function getVendorById($id)
    {
        return response()->json(['vendor' => ['id' => $id]]);
    }

    // Delete Vendor By ID
    public function deleteVendorById($id)
    {
        return response()->json(['message' => "Vendor $id deleted"]);
    }

    // Create Vendor Package
    public function createVendorPackage(Request $request)
    {
        return response()->json(['message' => 'Vendor package created']);
    }

    // Create Category (with file upload)
    public function createCategory(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageUrl = null;

        // Handle file upload to S3
        if ($request->hasFile('image')) {
            $path = $request->file('image')->storePublicly('categories');
            $imageUrl = Storage::disk('s3')->url($path);
        }

        Category::create([
            'type' => strtolower($request->type),
            'description' => $request->description,
            'image' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Category created successfully'
        ], 201);
    }


    public function GetAllCategories()
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories
        ], 200);
    }

    // Update Category
    public function updateCategory(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        if ($request->has('type')) {
            $category->type = strtolower($request->type);
        }

        if ($request->has('description')) {
            $category->description = $request->description;
        }

        // Handle image upload (S3)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 's3');
            $category->image = Storage::disk('s3')->url($path);
        }

        $category->save();

        return response()->json([
            'message' => 'Category updated',
            'category' => $category
        ], 200);
    }


    // Create Sub Category
    public function createSubCategory(Request $request)
    {
        if (!$request->type) {
            return response()->json(['message' => 'Sub category type is required'], 400);
        }

        $exists = SubCategory::where('type', strtolower($request->type))->exists();

        if ($exists) {
            return response()->json(['message' => 'Sub category already exists'], 400);
        }

        $imageUrl = null;

        // Optional image upload to S3
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sub-categories', 's3');
            $imageUrl = Storage::disk('s3')->url($path);
        }

        $subCategory = SubCategory::create([
            'type' => strtolower($request->type),
            'category_id' => $request->category_id, // matches your model
            'description' => $request->description,
            'image' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Sub category created successfully',
            'subCategory' => $subCategory
        ], 201);
    }


    public function GetSingleCategory()
    {

    }

    public function GetSubCategories()
    {

    }
}
