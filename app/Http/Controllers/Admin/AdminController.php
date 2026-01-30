<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use ChecksPermissions;
    /**
     * Display a listing of admins.
     */
    public function index(Request $request)
    {
        // If user doesn't have admins.view permission, redirect to their own profile
        if (!$this->hasPermission('admins.view')) {
            return redirect()->route('admin.admins.show', auth()->id());
        }
        
        $query = User::whereHas('role', function($q) {
            $q->where('name', 'admin')->orWhere('name', 'like', 'admin_%');
        })->with(['role', 'role.permissions']);

        // Search admins
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->orderBy('created_at', 'desc')->paginate(20);
        $allPermissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('admin.admins.index', compact('admins', 'allPermissions'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $this->authorizePermission('admins.create');
        
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $permissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('admin.admins.create', compact('adminRole', 'permissions'));
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('admins.create');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();

        try {
            $adminRole = Role::where('name', 'admin')->firstOrFail();

            $admin = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $adminRole->id,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            // Assign permissions if provided
            if (isset($validated['permissions']) && count($validated['permissions']) > 0) {
                // Create a new role for this admin with specific permissions
                $customRole = Role::create([
                    'name' => 'admin_' . $admin->id,
                    'description' => 'Custom admin role for ' . $admin->name,
                ]);

                // Get dashboard.view permission and ensure it's included
                $dashboardPermission = Permission::where('slug', 'dashboard.view')->first();
                $permissionsToAttach = $validated['permissions'];
                
                // Add dashboard.view permission if not already included
                if ($dashboardPermission && !in_array($dashboardPermission->id, $permissionsToAttach)) {
                    $permissionsToAttach[] = $dashboardPermission->id;
                }

                $customRole->permissions()->attach($permissionsToAttach);
                $admin->update(['role_id' => $customRole->id]);
                // Refresh the role relationship
                $admin->refresh();
                $admin->load('role.permissions');
            }

            DB::commit();

            return redirect()->route('admin.admins.show', $admin->id)
                ->with('success', 'Admin created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the admin: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified admin.
     */
    public function show($id)
    {
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'admin')->orWhere('name', 'like', 'admin_%');
        })->with(['role', 'role.permissions'])->findOrFail($id);

        // Allow admins to view their own profile, but require permission for others
        if (auth()->id() != $admin->id) {
            $this->authorizePermission('admins.view');
        }
        
        $allPermissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');
        $adminPermissions = $admin->role->permissions->pluck('id')->toArray();

        return view('admin.admins.show', compact('admin', 'allPermissions', 'adminPermissions'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit($id)
    {
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'admin')->orWhere('name', 'like', 'admin_%');
        })->with(['role', 'role.permissions'])->findOrFail($id);

        $isSelf = auth()->id() == $admin->id;
        
        // Allow admins to edit their own basic info without permission
        // But require admins.edit permission for editing others
        if (!$isSelf) {
            $this->authorizePermission('admins.edit');
        }
        
        // Check if user can edit permissions (either editing others or has admins.edit for self)
        $canEditPermissions = !$isSelf || $this->hasPermission('admins.edit');
        
        $permissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');
        $selectedPermissions = $admin->role->permissions->pluck('id')->toArray();

        return view('admin.admins.edit', compact('admin', 'permissions', 'selectedPermissions', 'canEditPermissions'));
    }

    /**
     * Update the specified admin.
     */
    public function update(Request $request, $id)
    {
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'admin')->orWhere('name', 'like', 'admin_%');
        })->with('role')->findOrFail($id);

        $isSelf = auth()->id() == $admin->id;
        $hasPermissions = isset($request->permissions) && count($request->permissions) > 0;
        
        // Allow admins to update their own basic info without permission
        // But require admins.edit permission for:
        // 1. Editing other admins
        // 2. Changing permissions (even for self, if they want to edit their own permissions)
        if (!$isSelf) {
            // Editing another admin always requires permission
            $this->authorizePermission('admins.edit');
        } elseif ($hasPermissions) {
            // Changing own permissions requires admins.edit permission
            $this->authorizePermission('admins.edit');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();

        try {
            // Update admin info
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $admin->update($updateData);

            // Handle permissions
            $adminRole = Role::where('name', 'admin')->first();
            $isSuperAdmin = $admin->role->name === 'admin';
            
            // Check if permissions were explicitly submitted in the request
            $permissionsSubmitted = $request->has('permissions');
            
            if ($permissionsSubmitted) {
                // Permissions were explicitly submitted (either checked or unchecked)
                if (isset($validated['permissions']) && count($validated['permissions']) > 0) {
                    // Permissions were selected - update them
                    $dashboardPermission = Permission::where('slug', 'dashboard.view')->first();
                    $permissionsToSync = $validated['permissions'];
                    
                    // Add dashboard.view permission if not already included
                    if ($dashboardPermission && !in_array($dashboardPermission->id, $permissionsToSync)) {
                        $permissionsToSync[] = $dashboardPermission->id;
                    }

                    // If super admin, check if custom role already exists or create new one
                    if ($isSuperAdmin) {
                        $customRoleName = 'admin_' . $admin->id;
                        $customRole = Role::where('name', $customRoleName)->first();
                        
                        if (!$customRole) {
                            // Create new custom role if it doesn't exist
                            $customRole = Role::create([
                                'name' => $customRoleName,
                                'description' => 'Custom admin role for ' . $admin->name,
                            ]);
                        } else {
                            // Update description if role exists
                            $customRole->update([
                                'description' => 'Custom admin role for ' . $admin->name,
                            ]);
                        }
                        
                        $customRole->permissions()->sync($permissionsToSync);
                        $admin->update(['role_id' => $customRole->id]);
                        // Refresh the role relationship
                        $admin->refresh();
                        $admin->load('role.permissions');
                    } else {
                        // Update existing custom role permissions
                        $admin->role->permissions()->sync($permissionsToSync);
                        // Update role description
                        $admin->role->update([
                            'description' => 'Custom admin role for ' . $admin->name,
                        ]);
                        // Refresh the role relationship to clear cache
                        $admin->refresh();
                        $admin->load('role.permissions');
                    }
                } else {
                    // Permissions array was submitted but empty - convert to super admin
                    if (!$isSuperAdmin) {
                        // Delete the custom role if it exists
                        if (str_starts_with($admin->role->name, 'admin_')) {
                            $oldRole = $admin->role;
                            $admin->update(['role_id' => $adminRole->id]);
                            // Delete the old custom role
                            $oldRole->permissions()->detach();
                            $oldRole->delete();
                        } else {
                            $admin->update(['role_id' => $adminRole->id]);
                        }
                        // Refresh the role relationship
                        $admin->refresh();
                        $admin->load('role');
                    }
                }
            }
            // If permissions were not submitted at all, keep existing permissions unchanged

            DB::commit();
            
            // If updating own account, refresh auth user's role cache
            if (auth()->id() == $admin->id) {
                auth()->user()->refresh();
                auth()->user()->load('role.permissions');
            }

            return redirect()->route('admin.admins.show', $admin->id)
                ->with('success', 'Admin updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the admin: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified admin.
     */
    public function destroy($id)
    {
        $this->authorizePermission('admins.delete');
        
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'admin')->orWhere('name', 'like', 'admin_%');
        })->with('role')->findOrFail($id);

        // Prevent deleting yourself
        if ($admin->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        DB::beginTransaction();

        try {
            // Delete custom role if exists
            if ($admin->role->name !== 'admin' && str_starts_with($admin->role->name, 'admin_')) {
                $admin->role->permissions()->detach();
                $admin->role->delete();
            }

            $admin->delete();

            DB::commit();

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the admin.');
        }
    }
}
