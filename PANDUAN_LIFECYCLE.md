# 🔄 Panduan Lifecycle Livewire & Task Manager

Panduan lengkap untuk memahami metode lifecycle Livewire dan arsitektur aplikasi Task Manager.

## 🏗️ Gambaran Umum Aplikasi

**Tech Stack:** Laravel 11 + Livewire 3 + Tailwind CSS + MySQL  
**Arsitektur:** TALL Stack (Tailwind, Alpine.js, Laravel, Livewire)  
**Pola:** UI reaktif berbasis komponen tanpa JavaScript

## 🔄 Penjelasan Lengkap Lifecycle Livewire

### 1. 🚀 Inisialisasi Komponen
```php
public function mount()
{
    // 🎯 KAPAN: Komponen pertama kali dimuat/diinstansiasi
    // 🎯 TUJUAN: Inisialisasi state komponen, set nilai default
    // 🎯 BERJALAN: Sekali per lifecycle komponen
    
    $this->resetForm(); // Set state awal form
}
```

### 2. 💧 Proses Hidrasi
```php
public function hydrate()
{
    // 🎯 KAPAN: Setelah komponen dipulihkan dari session
    // 🎯 TUJUAN: Hubungkan kembali service, pulihkan state
    // 🎯 BERJALAN: Setiap request setelah restore session
    
    // Contoh: Hubungkan kembali ke API eksternal
    // Contoh: Pulihkan computed properties
}

public function boot()
{
    // 🎯 KAPAN: Setiap request, sebelum metode lain
    // 🎯 TUJUAN: Setup state global, inisialisasi service
    // 🎯 BERJALAN: Metode pertama yang dipanggil setiap request
    
    // Contoh: Setup konteks authentication
    // Contoh: Load konfigurasi
}
```

### 3. ⚡ Lifecycle Update Property
```php
public function updating($property, $value)
{
    // 🎯 KAPAN: SEBELUM property diupdate (interceptor)
    // 🎯 TUJUAN: Sanitasi input, validasi, transformasi data
    // 🎯 RETURN: Nilai yang dimodifikasi atau nilai asli
    // 🎯 BISA: Mencegah update dengan return nilai berbeda
    
    if ($property === 'newTaskTitle') {
        return trim($value); // Bersihkan whitespace
    }
    
    if ($property === 'newTagColor' && !str_starts_with($value, '#')) {
        return '#' . $value; // Pastikan format hex
    }
    
    return $value;
}

public function updated($property, $value)
{
    // 🎯 KAPAN: SETELAH property diupdate (reaktif)
    // 🎯 TUJUAN: Side effects, validasi, trigger update
    // 🎯 RETURN: void (property sudah berubah)
    // 🎯 TIDAK BISA: Mengubah nilai property
    
    match($property) {
        'newTaskTitle' => $this->validateTaskTitle(),
        'newTaskPriority' => $this->validateTaskPriority(),
        'selectedTags' => $this->handleTagsUpdate(),
        default => null
    };
}
```

### 4. 🎨 Proses Rendering
```php
public function rendering()
{
    // 🎯 KAPAN: Sebelum metode render() dipanggil
    // 🎯 TUJUAN: Siapkan data untuk view, bersihkan state sementara
    // 🎯 BERJALAN: Setiap kali komponen perlu re-render
    
    $this->clearValidationErrors();
}

public function rendered()
{
    // 🎯 KAPAN: Setelah HTML dibuat dan dikirim ke browser
    // 🎯 TUJUAN: Interaksi JavaScript, manipulasi DOM
    // 🎯 BERJALAN: Setelah setiap siklus render
    
    $this->dispatch('component-rendered');
}
```

### 5. 🏪 Proses Dehidrasi
```php
public function dehydrate()
{
    // 🎯 KAPAN: Sebelum state komponen disimpan ke session
    // 🎯 TUJUAN: Bersihkan data sensitif, siapkan untuk penyimpanan
    // 🎯 BERJALAN: Di akhir request sebelum penyimpanan session
    
    if ($this->showAddTagModal || $this->showDeleteModal) {
        $this->dispatch('modal-state-changed');
    }
}
```

### 6. ⚠️ Penanganan Exception
```php
public function exception($e, $stopPropagation)
{
    // 🎯 KAPAN: Metode komponen manapun throw exception
    // 🎯 TUJUAN: Penanganan error yang elegan, feedback user
    // 🎯 BERJALAN: Ketika error terjadi di metode komponen
    
    $this->dispatch('notify-error', 'Terjadi kesalahan: ' . $e->getMessage());
    $this->resetForm(); // Reset ke state yang aman
    $stopPropagation(); // Jangan biarkan Laravel menanganinya
}
```

## 📊 Diagram Alur Lifecycle
```
Mulai Request
     ↓
[boot()] ← Dipanggil pertama, setiap request
     ↓
[hydrate()] ← Pulihkan dari session
     ↓
[updating()] ← Sebelum property berubah
     ↓
Update Property
     ↓
[updated()] ← Setelah property berubah
     ↓
[rendering()] ← Sebelum view render
     ↓
[render()] ← Generate HTML
     ↓
[rendered()] ← Setelah HTML dikirim
     ↓
[dehydrate()] ← Sebelum simpan session
     ↓
Selesai Request
```

## 🎯 Properties Komponen Mendalam

### Public Properties (State Reaktif)
```php
// 🔄 REAKTIF: Perubahan otomatis update UI
public $newTaskTitle = '';           // ← Terikat ke input field
public $newTaskPriority = '';        // ← Terikat ke radio buttons  
public $selectedTags = [];           // ← Terikat ke checkboxes
public $editingTaskId = null;        // ← Kontrol mode edit
public $showTagsDropdown = false;    // ← Kontrol visibilitas dropdown

// 🎯 WIRE MODEL BINDING:
// wire:model="newTaskTitle" ← Two-way binding
// wire:model.live="selectedTags" ← Update instan
// wire:model.lazy="newTaskTitle" ← Update saat blur
```

### Computed Properties (Data Dinamis)
```php
public function getTasksProperty()
{
    // 🎯 AKSES: $this->tasks di PHP, $tasks di Blade
    // 🎯 CACHING: Di-cache sampai komponen re-render
    // 🎯 REFRESH: Auto-refresh ketika dependencies berubah
    
    return Task::with('tags')    // ← Eager load relationships
        ->active()               // ← Hanya task yang belum selesai
        ->latest()              // ← Terbaru dulu
        ->get();
}

// 🔄 PENGGUNAAN DI BLADE:
// @foreach($this->tasks as $task) ← Akses computed property
// {{ count($this->tasks) }} ← Gunakan dalam expression
```

## 🎬 Penjelasan Metode Action

### Alur Manajemen Task
```php
public function addTask()
{
    // 🎯 TRIGGER: wire:click="addTask"
    // 🎯 ALUR: Validasi → Buat → Lampirkan → Reset → Notifikasi
    
    // 1️⃣ VALIDASI
    $this->validate($this->getTaskValidationRules());

    try {
        // 2️⃣ BUAT TASK
        $task = Task::create([
            'title' => $this->newTaskTitle,
            'priority' => $this->newTaskPriority,
            'is_completed' => false,
        ]);

        // 3️⃣ LAMPIRKAN TAG (Many-to-Many)
        if (!empty($this->selectedTags)) {
            $task->tags()->attach($this->selectedTags);
        }

        // 4️⃣ FEEDBACK SUKSES
        $this->dispatch('task-created', $task->id);
        
        // 5️⃣ RESET FORM
        $this->resetForm();
        
    } catch (\Exception $e) {
        // 6️⃣ PENANGANAN ERROR
        $this->addError('newTaskTitle', 'Gagal membuat task');
    }
}
```

### Sistem Inline Editing
```php
public function startEditing($taskId, $currentTitle)
{
    // 🎯 TRIGGER: Klik pada judul task
    $this->editingTaskId = $taskId;      // ← Tandai task mana
    $this->editingTaskTitle = $currentTitle; // ← Pre-fill nilai
}

public function updateTask()
{
    // 🎯 TRIGGER: Tekan Enter atau klik save
    // 🎯 KEYS: wire:keydown.enter="updateTask"
    
    $this->validate(['editingTaskTitle' => 'required|min:3|max:255']);
    
    $task = Task::findOrFail($this->editingTaskId);
    $task->update(['title' => $this->editingTaskTitle]);
    
    // Keluar dari mode edit
    $this->reset(['editingTaskId', 'editingTaskTitle']);
}

public function cancelEditing()
{
    // 🎯 TRIGGER: Tekan Escape atau klik cancel
    // 🎯 KEYS: wire:keydown.escape="cancelEditing"
    
    $this->reset(['editingTaskId', 'editingTaskTitle']);
}
```

## 🎨 Integrasi Template Blade

### Contoh Property Binding
```blade
{{-- ✅ REACTIVE INPUT BINDING --}}
<x-form.input
    wireModel="newTaskTitle"           {{-- Two-way binding --}}
    placeholder="Apa yang perlu dikerjakan?"
    :error="$errors->first('newTaskTitle')"
/>

{{-- ✅ LIVE CHECKBOX BINDING --}}
<input 
    type="checkbox" 
    wire:model.live="selectedTags"     {{-- Update instan --}}
    value="{{ $tag->id }}"
/>

{{-- ✅ CONDITIONAL RENDERING --}}
@if($editingTaskId === $task->id)
    {{-- Mode Edit --}}
    <x-form.input 
        wireModel="editingTaskTitle"
        wire:keydown.enter="updateTask"
        wire:keydown.escape="cancelEditing"
    />
@else
    {{-- Mode Tampil --}}
    <h3 wire:click="startEditing({{ $task->id }}, '{{ addslashes($task->title) }}')">
        {{ $task->title }}
    </h3>
@endif
```

### Pola Event Handling
```blade
{{-- ✅ PEMANGGILAN METODE --}}
<button wire:click="addTask">Tambah Task</button>
<button wire:click="completeTask({{ $task->id }})">Selesai</button>
<button wire:click="confirmDelete({{ $task->id }})">Hapus</button>

{{-- ✅ KEYBOARD EVENTS --}}
<input 
    wire:keydown.enter="updateTask"     {{-- Simpan dengan Enter --}}
    wire:keydown.escape="cancelEditing" {{-- Cancel dengan Escape --}}
/>

{{-- ✅ MANIPULASI PROPERTY --}}
<button wire:click="$set('newTaskPriority', 'high')">Prioritas Tinggi</button>
<button wire:click="$toggle('showTagsDropdown')">Toggle Dropdown</button>
<button wire:click="$refresh">Refresh Komponen</button>
```

### Optimisasi Loop
```blade
{{-- ✅ LOOP YANG DIOPTIMALKAN --}}
@forelse($this->tasks as $task)
    <div wire:key="task-{{ $task->id }}"> {{-- Penting untuk update --}}
        <h3>{{ $task->title }}</h3>
        
        {{-- Relasi tags --}}
        @foreach($task->tags as $tag)
            <span 
                wire:key="tag-{{ $tag->id }}-task-{{ $task->id }}"
                style="background-color: {{ $tag->color }}"
            >
                {{ $tag->name }}
            </span>
        @endforeach
    </div>
@empty
    <div class="text-center py-8">
        <p>Belum ada task! Tambahkan task pertama Anda di atas.</p>
    </div>
@endforelse
```

## 🔧 Pola Lanjutan

### Sistem Event
```php
// 📤 MENGIRIM EVENT
$this->dispatch('task-created', $task->id);        // Ke browser
$this->dispatch('notify', 'Pesan sukses');         // Ke komponen lain
$this->dispatchTo('KomponentLain', 'refresh');     // Ke komponen spesifik

// 📥 MENDENGARKAN EVENT
#[On('task-created')]
public function onTaskCreated($taskId)
{
    $this->dispatch('notify', 'Task berhasil dibuat!');
}

#[On('refresh-tasks')]
public function refreshTasks()
{
    unset($this->tasks); // Paksa recompute computed property
}
```

### Pola Validasi
```php
// 🔍 VALIDASI REAL-TIME
protected function validateTaskTitle()
{
    if (!empty($this->newTaskTitle)) {
        $this->resetErrorBag('newTaskTitle'); // Hapus error saat user mengetik
    }
}

// 📋 ATURAN VALIDASI
public function getTaskValidationRules(): array
{
    return [
        'newTaskTitle' => 'required|string|min:3|max:255',
        'newTaskPriority' => 'required|in:low,medium,high',
        'selectedTags' => 'nullable|array',
        'selectedTags.*' => 'exists:tags,id',
    ];
}

// ⚠️ PENANGANAN ERROR
try {
    $this->validate($rules);
    // ... logika sukses
} catch (ValidationException $e) {
    // Livewire menangani ini otomatis
} catch (\Exception $e) {
    $this->addError('field', 'Pesan error custom');
}
```

### Optimisasi Performa
```php
// ✅ UPDATE EFISIEN
$this->reset(['editingTaskId', 'editingTaskTitle']); // Hanya props spesifik
unset($this->tasks); // Paksa recompute computed property

// ✅ LAZY LOADING
public function loadTasks()
{
    // Load data berat hanya ketika diperlukan
    $this->tasks = Task::with('tags')->get();
}

// ✅ CONDITIONAL RENDERING
@if($this->showExpensiveComponent)
    @livewire('expensive-component')
@endif
```

## 🗄️ Integrasi Database

### Relasi Eloquent
```php
// 📋 MODEL TASK
class Task extends Model
{
    protected $fillable = ['title', 'priority', 'is_completed'];
    protected $casts = ['is_completed' => 'boolean'];
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_completed', false);
    }
}

// 🏷️ MODEL TAG
class Tag extends Model
{
    protected $fillable = ['name', 'color'];
    
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_tag');
    }
}
```

### Operasi Database
```php
// ✅ BUAT DENGAN RELASI
$task = Task::create([
    'title' => $this->newTaskTitle,
    'priority' => $this->newTaskPriority,
]);
$task->tags()->attach($this->selectedTags); // Many-to-many

// ✅ QUERY DENGAN RELASI
Task::with('tags')              // Eager load untuk mencegah N+1
    ->active()                  // Gunakan scope
    ->latest()                  // Order by created_at desc
    ->get();

// ✅ UPDATE RELASI
$task->tags()->sync($newTagIds);    // Ganti semua tag
$task->tags()->detach($tagId);      // Hapus tag spesifik
$task->tags()->attach($tagId);      // Tambah tag spesifik
```

## 🎯 Ringkasan Best Practices

### ✅ LAKUKAN
- Gunakan `wire:key` dalam loop untuk performa
- Validasi semua input user
- Tangani exception dengan elegan  
- Gunakan computed properties untuk data dinamis
- Reset form setelah operasi sukses
- Gunakan reset property spesifik daripada reset komponen penuh

### ❌ JANGAN LAKUKAN
- Taruh logika berat di metode `updated()`
- Lupa `wire:key` dalam daftar dinamis
- Gunakan computed properties (`getXProperty`) untuk operasi mahal
- Abaikan error handling dalam try-catch blocks
- Gunakan public properties untuk data sensitif

### 🎯 Tips Performa
- Gunakan modifier `.lazy` untuk input non-kritis
- Gunakan `.live` hanya ketika feedback langsung diperlukan
- Implementasi loading states untuk operasi lambat
- Cache computed properties yang mahal
- Gunakan index database untuk kolom yang sering di-query

## 🎊 Sistem Event Dispatch Yang Diimplementasi

Semua event dispatch dalam aplikasi ini sekarang memiliki handler JavaScript yang nyata dengan efek visual dan fungsionalitas yang lengkap.

### 📱 Sistem Notifikasi
```javascript
// Fungsi notifikasi utama
function showNotification(message, type = 'success') {
    // 🎯 TIPE: 'success' (hijau), 'error' (merah), 'info' (biru)
    // 🎯 FITUR: Auto-dismiss 5 detik, tombol close manual
    // 🎯 ANIMASI: Slide in/out dengan smooth transition
    // 🎯 POSISI: Fixed top-right corner dengan z-index tinggi
}
```

### 🎯 Event Handlers Yang Diimplementasi

#### 1. **Event Notifikasi**
```javascript
// ✅ SUCCESS NOTIFICATION
Livewire.on('notify', (message) => {
    showNotification(message, 'success');
    console.log('✅ Success:', message);
});

// ❌ ERROR NOTIFICATION  
Livewire.on('notify-error', (message) => {
    showNotification(message, 'error');
    console.error('❌ Error:', message);
});
```

#### 2. **Event Task Management**
```javascript
// 📝 TASK CREATED - Confetti celebration!
Livewire.on('task-created', (taskId) => {
    showNotification(`Task berhasil dibuat! ID: ${taskId}`, 'success');
    console.log('📝 Task Created:', taskId);
    
    // 🎊 EFEK CONFETTI
    if (window.confetti) {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
    }
});

// ✅ TASK COMPLETED - Celebration message
Livewire.on('task-completed', (taskId) => {
    showNotification(`Task selesai! Kerja bagus! 🎉`, 'success');
    console.log('✅ Task Completed:', taskId);
});
```

#### 3. **Event Tag Management**
```javascript
// 🏷️ TAG CREATED
Livewire.on('tag-created', (tagId) => {
    showNotification(`Tag baru berhasil dibuat!`, 'info');
    console.log('🏷️ Tag Created:', tagId);
});

// 🏷️ TAGS UPDATED - Visual feedback
Livewire.on('tags-updated', (count) => {
    console.log(`🏷️ Tags Updated: ${count} tags selected`);
    
    // EFEK VISUAL: Ring biru pada tombol tags
    if (count > 0) {
        const tagButton = document.querySelector('[wire\\:click*="showTagsDropdown"]');
        if (tagButton) {
            tagButton.classList.add('ring-2', 'ring-blue-500');
            setTimeout(() => {
                tagButton.classList.remove('ring-2', 'ring-blue-500');
            }, 1000);
        }
    }
});
```

#### 4. **Event Komponen**
```javascript
// 🔄 COMPONENT RENDERED - Auto focus
Livewire.on('component-rendered', () => {
    console.log('🔄 Component Rendered');
    
    // AUTO FOCUS pada input task jika kosong
    const firstInput = document.querySelector('input[wire\\:model*="newTaskTitle"]');
    if (firstInput && !firstInput.value) {
        setTimeout(() => firstInput.focus(), 100);
    }
});

// 🔄 MODAL STATE CHANGED - Backdrop effect
Livewire.on('modal-state-changed', () => {
    console.log('🔄 Modal State Changed');
    
    // EFEK BACKDROP BLUR
    const body = document.body;
    const hasOpenModal = document.querySelector('[x-show="open"]');
    
    if (hasOpenModal) {
        body.classList.add('modal-open'); // Blur + disable scroll
    } else {
        body.classList.remove('modal-open');
    }
});
```

### ⌨️ Keyboard Shortcuts
```javascript
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + K untuk focus input task
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const taskInput = document.querySelector('input[wire\\:model*="newTaskTitle"]');
        if (taskInput) {
            taskInput.focus();
            showNotification('Task input focused! Start typing...', 'info');
        }
    }
    
    // Escape untuk close semua modal/dropdown
    if (e.key === 'Escape') {
        Livewire.dispatch('close-modals');
    }
});
```

### 🔄 Smart Features
```javascript
// AUTO REFRESH ketika user kembali ke tab
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        console.log('🔄 Page became visible - refreshing tasks');
        Livewire.dispatch('refresh-tasks');
    }
});
```

### 🎨 Efek Visual Yang Diimplementasi

#### 1. **Sistem Notifikasi**
- **Posisi**: Fixed top-right corner
- **Animasi**: Smooth slide in dari kanan, slide out ke kanan
- **Durasi**: Auto-dismiss setelah 5 detik
- **Interaksi**: Manual close dengan tombol X
- **Styling**: Color-coded dengan icon yang sesuai

#### 2. **Efek Confetti**
- **Trigger**: Saat task berhasil dibuat
- **Library**: canvas-confetti from CDN
- **Setting**: 100 partikel, spread 70°, dari center-bottom

#### 3. **Ring Effect**
- **Trigger**: Saat tags dipilih/diupdate
- **Visual**: Ring biru pada tombol tags
- **Durasi**: 1 detik kemudian hilang

#### 4. **Modal Backdrop**
- **Efek**: Blur background + disable body scroll
- **CSS**: backdrop-filter: blur(2px) + rgba overlay

### 🧪 Cara Testing Event System

#### 1. **Test Notifikasi Success**
```javascript
// Di browser console:
Livewire.dispatch('notify', 'Test pesan sukses');
// Expected: Notifikasi hijau muncul dengan ✅
```

#### 2. **Test Notifikasi Error**
```javascript
// Di browser console:
Livewire.dispatch('notify-error', 'Test pesan error');
// Expected: Notifikasi merah muncul dengan ❌
```

#### 3. **Test Confetti Effect**
- Buat task baru dengan mengisi form
- Klik "Add Task"
- Expected: Confetti + notifikasi sukses

#### 4. **Test Keyboard Shortcuts**
- Tekan `Ctrl/Cmd + K` → Input task harus focus + notifikasi info
- Tekan `Escape` → Semua modal/dropdown harus close

#### 5. **Test Visual Effects**
- Pilih beberapa tags → Tombol tags harus ada ring biru
- Buka modal → Background harus blur
- Complete task → Notifikasi celebration

### 📊 Console Monitoring

Buka DevTools Console untuk melihat semua event:
```
✅ Success: Task created successfully!
📝 Task Created: 123
🏷️ Tags Updated: 2 tags selected  
🔄 Component Rendered
🔄 Modal State Changed
🔄 Page became visible - refreshing tasks
```

### 🎯 Implementasi dalam Livewire Component

Semua dispatch event di component sudah terhubung:
```php
// ✅ SUDAH ADA HANDLER
$this->dispatch('task-created', $task->id);        // → Confetti + notifikasi
$this->dispatch('notify', 'Success message');       // → Notifikasi hijau
$this->dispatch('notify-error', 'Error message');   // → Notifikasi merah
$this->dispatch('tags-updated', count($tags));      // → Ring effect
$this->dispatch('component-rendered');              // → Auto focus
$this->dispatch('modal-state-changed');             // → Backdrop blur
```

## 🚀 Event System Benefits

1. **User Experience**: Feedback visual langsung untuk setiap aksi
2. **Developer Experience**: Console logging untuk debugging
3. **Accessibility**: Keyboard shortcuts dan auto-focus
4. **Performance**: Smart refresh hanya saat diperlukan
5. **Polish**: Animasi dan efek yang smooth dan professional

Panduan ini memberikan pemahaman lengkap tentang metode lifecycle Livewire dan bagaimana mereka menggerakkan fitur reaktif aplikasi Task Manager dengan sistem event yang lengkap dan interaktif.