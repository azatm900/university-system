<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(protected AuditService $audit) {}

    public function index(Request $request): View
    {
        $q = User::query();
        if ($role = $request->input('role')) {
            $q->where('role', $role);
        }
        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $q->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }
        if ($search = $request->input('search')) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $q->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

   public function create(Request $request): View
{
    $preselectedRole = $request->query('role');
    return view('admin.users.create', [
        'roles' => UserRole::cases(),
        'preselectedRole' => $preselectedRole,
    ]);
}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8'],
            'role'      => ['required', Rule::enum(UserRole::class)],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => $data['password'],
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? true,
        ]);
        $this->audit->log(
            action: 'user.created',
            subject: $user,
            newValues: [
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->getRawOriginal('role'),
                'is_active' => (bool) $user->getRawOriginal('is_active'),
            ],
        );
        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => UserRole::cases()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::enum(UserRole::class)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $old = [
            'name'  => $user->getRawOriginal('name'),
            'email' => $user->getRawOriginal('email'),
            'role'  => $user->getRawOriginal('role'),
        ];
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->role  = $data['role'];
        if (array_key_exists('is_active', $data)) {
            $user->is_active = (bool) $data['is_active'];
        }
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();
        $this->audit->log(
            action: 'user.updated',
            subject: $user,
            oldValues: $old,
            newValues: [
                'name'  => $user->getRawOriginal('name'),
                'email' => $user->getRawOriginal('email'),
                'role'  => $user->getRawOriginal('role'),
            ],
        );
        return redirect()->route('admin.users.index')->with('success', 'تم التحديث');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user() && $request->user()->id === $user->id) {
            abort(403, 'Cannot delete yourself');
        }
        $snapshot = [
            'name'  => $user->getRawOriginal('name'),
            'email' => $user->getRawOriginal('email'),
        ];
        $user->delete();
        $this->audit->log(
            action: 'user.deleted',
            subject: $user,
            oldValues: $snapshot,
        );
        return redirect()->route('admin.users.index')->with('success', 'تم الحذف');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($request->user() && $request->user()->id === $user->id) {
            abort(403, 'Cannot toggle your own status');
        }
        $old = (bool) $user->getRawOriginal('is_active');
        $user->is_active = !$old;
        $user->save();
        $this->audit->log(
            action: 'user.toggled_active',
            subject: $user,
            oldValues: ['is_active' => $old],
            newValues: ['is_active' => (bool) $user->getRawOriginal('is_active')],
        );
        return redirect()->route('admin.users.index')->with('success', 'تم التبديل');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->password = $data['password'];
        $user->save();
        $this->audit->log(
            action: 'user.password_reset',
            subject: $user,
            newValues: ['reset_at' => now()->toIso8601String()],
        );
        return redirect()->route('admin.users.index')->with('success', 'تم إعادة تعيين كلمة المرور');
    }
    public function showResetPasswordForm(User $user): View
    {
        return view('admin.users.reset-password', compact('user'));
    }
}