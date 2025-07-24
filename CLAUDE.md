# Laravel TALL Stack Code Convention Configuration

## Project Context

You are working on a Laravel project using the TALL Stack (Tailwind, Alpine.js, Laravel, Livewire). Follow these conventions strictly to ensure code consistency, maintainability, and adherence to Clean Code, SOLID Principles, and MVC concepts.

## Tech Stack

* **Laravel**: Latest version (10.x or 11.x)
* **Livewire**: Latest version (3.x) Server-side rendering with SPA support
* **Alpine.js**: Lightweight JS for interactivity
* **Tailwind CSS**: Utility-first CSS framework

## Language Conventions

* **Primary Language**: English
* **Database/Models**: English naming
* **Comments**: Use English unless necessary in Indonesian

## Database Conventions

### Table Naming

* Format: English, plural, lowercase, snake\_case
* Examples: `students`, `student_lessons`, `user_permissions`

### Indexing

* Index columns used in WHERE clauses
* Use compound indexes for frequent multi-column queries

### ERD

* Use [dbdiagram.io](https://dbdiagram.io)
* Document all relationships and constraints

## Project Structure

### Modular Approach

```
modules/
└── [ModuleName]/
    ├── App/
    │   ├── Models/
    │   ├── Queries/
    │   ├── Services/
    │   └── Traits/
    ├── Database/
    │   ├── migrations/
    │   └── seeders/
    ├── Resources/
    │   └── views/
    │       └── livewire/
    └── Routes/
        └── [role_name].php
```

### Model Conventions

* Naming: English, singular, PascalCase (e.g. `Student.php`)
* Located in: `modules/[ModuleName]/App/Models/`
* Include relationships, scopes, and a `baseQuery()`

```php
class Student extends Model {
    protected $fillable = ['name', 'email', 'branch_id'];

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function permissions() {
        return $this->hasMany(StudentPermission::class);
    }

    public function scopeActive($query) {
        return $query->where('status', 'active');
    }

    public static function baseQuery($branchId = null, $status = null) {
        return self::when($branchId, fn($q, $b) => $q->where('branch_id', $b))
                   ->when($status, fn($q, $s) => $q->where('status', $s));
    }
}
```

### Query Classes

* Naming: `[ModelName]Query.php`
* Location: `modules/[ModuleName]/App/Queries/`
* Naming pattern: `get[Plural]()`, `paginate[Plural]()`, `fetch[Singular]()`, etc.

### Service Classes

* Handle business logic
* Naming: Based on functionality (e.g. `TahfidzExamService.php`)
* Use constructor-based dependency injection

### Traits

* Reusable methods grouped by functionality
* Naming: E.g. `UploadFileTrait.php`
* Location: `modules/[ModuleName]/App/Traits/`

## Livewire Conventions

### Component Naming

Follows menu structure:

```
php artisan make:livewire Role/Module/ParentMenu/ChildMenu/ComponentName
```

Example:

```
php artisan make:livewire KesantrianLeader/Permissions/Monitorings/Details/DetailMonitoringPermissionKesantrianLeader
```

### Class Structure

1. `#[Title('Page Title')]`
2. Public properties
3. Protected properties
4. Computed properties
5. Event listeners
6. Lifecycle hooks
7. Actions
8. `render()`

### Property Naming

```php
public $isRegistered, $items = [], $fetchSelectedStudent;
public $learningSchedules, $lessonHistories, $activeYear;
```

## Blade View Conventions

### File Organization

```
resources/views/
├── components/
│   ├── buttons/
│   ├── forms/
│   └── cards/
├── layouts/
│   ├── app.blade.php
│   ├── guest.blade.php
│   └── partials/
│       ├── headers/
│       ├── sidebars/
│       └── scripts/
├── errors/
└── livewire/
    └── [role-access]/
        └── [module-section]/
            └── [parent-menu]/
                └── [child-menu]/
```

### Blade Naming

* Use kebab-case
* Example: `detail-monitoring-permission-kesantrian-leader.blade.php`

### Tailwind UI Usage

#### Buttons

```blade
<button class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
    Save Changes
</button>
```

#### Forms

```blade
<label class="block text-sm font-medium text-gray-700">Student Name</label>
<input type="text" wire:model="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
```

#### Modals

Use Livewire and Tailwind:

```blade
<div x-data="{ open: @entangle('showModal') }">
    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded shadow max-w-2xl w-full">
            <h2 class="text-lg font-semibold">Edit Student</h2>
            <div class="mt-4">
                <!-- Modal Content -->
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button class="px-4 py-2 bg-gray-200 rounded" @click="open = false">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded" wire:click="save">Save</button>
            </div>
        </div>
    </div>
</div>
```

#### Tables

```blade
<table class="min-w-full text-sm">
    <thead>
        <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $student)
        <tr>
            <td class="border-t px-4 py-2">{{ $student->name }}</td>
            <td class="border-t px-4 py-2">
                <span class="px-2 py-1 rounded text-white {{ $student->status === 'active' ? 'bg-green-500' : 'bg-yellow-500' }}">
                    {{ $student->status }}
                </span>
            </td>
            <td class="border-t px-4 py-2">
                <button class="text-blue-500 hover:underline">Edit</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

#### Cards

```blade
<div class="bg-white shadow rounded-lg p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold">Student Information</h3>
        <p class="text-sm text-gray-600">Manage student details and permissions</p>
    </div>
    <div>
        <!-- Card content here -->
    </div>
</div>
```

## Routing Conventions

```php
Route::group(['middleware' => ['auth', 'student'], 'prefix' => 'student', 'as' => 'student.'], function () {
    Route::get('/dashboard', DashboardStudent::class)->name('dashboard');
    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/detail/{id}', DetailProfileStudent::class)->name('detail');
    });
});
```

* Naming: dot notation (e.g. `student.profile.detail`)
* URLs: kebab-case (`/student-permissions`)

## Middleware Setup

```php
'auth' => AuthCheck::class,
'guest' => RedirectIfAuthenticated::class,
'admin' => EnsureUserRole::class.':1',
'button' => EnsureUserRole::class.':2',
```

## Helper Classes

* Location: `app/Helpers/`
* Naming: `[Function]Helper.php`
* Example: `DateHelper.php`, `PermissionHelper.php`

# important-instruction-reminders
Do what has been asked; nothing more, nothing less.
NEVER create files unless they're absolutely necessary for achieving your goal.
ALWAYS prefer editing an existing file to creating a new one.
NEVER proactively create documentation files (*.md) or README files. Only create documentation files if explicitly requested by the User.

## Task Manager Application Setup

### Running the Application

To run the Task Manager application:

1. Install dependencies:
   ```bash
   composer install
   npm install
   ```

2. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Run migrations:
   ```bash
   php artisan migrate
   ```

4. Build assets:
   ```bash
   npm run build
   ```

5. Start the server:
   ```bash
   php artisan serve
   ```

### Features Implemented

✅ **Task Creation** - Add new tasks with title and priority selection (low, medium, high)
✅ **Tag Management** - Create and assign colorful tags to tasks via dropdown
✅ **Add Tag Modal** - Dynamic tag creation with color picker
✅ **Inline Editing** - Click task title to edit, save with Enter key
✅ **Task Completion** - Checkbox to mark tasks as completed (removes from list)
✅ **Delete Confirmation** - Modal popup for task deletion
✅ **Conditional Add Button** - Only active when title and priority are filled
✅ **Beautiful UI** - Doodle-inspired design with Tailwind CSS
✅ **Responsive Design** - Mobile-friendly interface
✅ **Priority Indicators** - Color-coded priority levels (green/yellow/red)
✅ **Tag Display** - Colored tag badges on tasks
✅ **Empty State** - Friendly message when no tasks exist

### Technical Stack

- **Framework**: Laravel 12.x
- **Frontend**: Livewire 3.x + Alpine.js + Tailwind CSS
- **Database**: SQLite (default)
- **Architecture**: TALL Stack (Tailwind, Alpine, Laravel, Livewire)