# 📝 Task Manager - Aplikasi Manajemen Tugas

Aplikasi manajemen tugas yang indah dan modern dibangun dengan Laravel TALL Stack (Tailwind CSS, Alpine.js, Laravel, Livewire) dan database MySQL.

## 🚀 Fitur Utama

### ✨ **Interface Pengguna**
- **Desain Terpusat**: Semua elemen form dan konten ter-align center dengan indah
- **UI Reaktif**: Update real-time tanpa refresh halaman
- **Animasi Smooth**: Transisi dan efek visual yang halus
- **Responsive Design**: Bekerja sempurna di desktop dan mobile

### 📋 **Manajemen Tugas**
- **Buat Tugas**: Tambah tugas dengan judul dan prioritas
- **Sistem Prioritas**: Low, Medium, High dengan indikator warna
- **Inline Editing**: Klik judul untuk edit, Enter untuk simpan, Escape untuk batal
- **Centang Selesai**: Checkbox untuk menandai tugas selesai
- **Hapus Tugas**: Konfirmasi modal sebelum menghapus

### 🏷️ **Sistem Tag**
- **Dropdown Tag**: Pilih dari tag yang sudah ada
- **Buat Tag Baru**: Modal untuk membuat tag dengan nama dan warna custom
- **Tag Berwarna**: Setiap tag memiliki warna hex yang dapat disesuaikan
- **Many-to-Many**: Satu tugas bisa memiliki banyak tag

### 🎊 **Sistem Notifikasi & Event**
- **Notifikasi Toast**: Success (hijau), Error (merah), Info (biru)
- **Efek Confetti**: Meledak saat berhasil membuat tugas
- **Visual Feedback**: Ring effect pada tombol tag saat dipilih
- **Keyboard Shortcuts**: Ctrl+K untuk focus input, Escape untuk close modal

## 🏗️ Arsitektur Teknologi

### **Tech Stack**
- **Laravel 11** - Backend PHP framework
- **Livewire 3** - Full-stack reactive components  
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **MySQL** - Relational database

### **Arsitektur Pattern**
- **TALL Stack** - Tailwind, Alpine.js, Laravel, Livewire
- **Component-Based** - UI komponen yang dapat digunakan ulang
- **MVC Pattern** - Model-View-Controller separation
- **Reactive Programming** - Real-time UI updates

## 📁 Struktur Proyek

```
TaskManager/
├── app/
│   ├── Livewire/User/TaskManager/
│   │   └── ManageTasksTaskManagerUser.php    # Komponen Livewire utama
│   ├── Models/
│   │   ├── Task.php                          # Model Eloquent Task
│   │   └── Tag.php                           # Model Eloquent Tag
│   └── Traits/
│       ├── ValidationHelperTrait.php         # Helper validasi
│       └── ModalHandlerTrait.php             # Manajemen state modal
├── database/
│   └── migrations/
│       ├── *_create_tasks_table.php          # Schema tabel tasks
│       ├── *_create_tags_table.php           # Schema tabel tags
│       └── *_create_task_tag_table.php       # Tabel pivot many-to-many
├── resources/views/
│   ├── components/form/                      # Komponen form reusable
│   │   ├── input.blade.php                   # Input field component
│   │   ├── button.blade.php                  # Button component
│   │   └── priority-selector.blade.php       # Priority selector
│   ├── layouts/
│   │   └── app.blade.php                     # Layout utama + event handlers
│   └── livewire/user/task-manager/
│       └── manage-tasks-task-manager-user.blade.php
└── routes/
    ├── user.php                              # Route user
    ├── admin.php                             # Route admin
    └── guest.php                             # Route demo
```

## 🗄️ Skema Database

### **Tabel Tasks**
```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_completed_created (is_completed, created_at),
    INDEX idx_priority (priority)
);
```

### **Tabel Tags**
```sql
CREATE TABLE tags (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    color VARCHAR(7) DEFAULT '#3B82F6',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_name (name)
);
```

### **Tabel Pivot Task-Tag**
```sql
CREATE TABLE task_tag (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_task_tag (task_id, tag_id),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
```

## ⚙️ Instalasi & Setup

### **1. Clone Repository**
```bash
git clone <repository-url>
cd TaskManager
```

### **2. Install Dependencies**
```bash
# Install Composer dependencies
composer install

# Install NPM dependencies
npm install
```

### **3. Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### **4. Database Configuration**
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=your_password
```

### **5. Database Migration**
```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE task_manager"

# Jalankan migrasi
php artisan migrate
```

### **6. Build Assets**
```bash
# Development
npm run dev

# Production
npm run build
```

### **7. Start Development Server**
```bash
php artisan serve
```

Aplikasi akan tersedia di `http://localhost:8000`

## 🎯 Cara Penggunaan

### **1. Akses Route**
- **User Tasks**: `/tasks` - Interface utama untuk user
- **Admin Tasks**: `/admin/tasks` - Interface admin
- **Demo**: `/demo/tasks` - Demo tanpa autentikasi

### **2. Membuat Tugas**
1. Isi judul tugas di input field
2. Pilih prioritas (Low/Medium/High)
3. Pilih tag dari dropdown (opsional)
4. Klik "✨ Add Task"
5. Nikmati efek confetti! 🎊

### **3. Mengelola Tag**
1. Klik tombol "🏷️ Tags" untuk buka dropdown
2. Centang tag yang ingin dipilih
3. Klik "+ Add New Tag" untuk buat tag baru
4. Isi nama dan pilih warna
5. Tag otomatis terpilih setelah dibuat

### **4. Edit Tugas**
1. Klik pada judul tugas untuk mulai edit
2. Ketik judul baru
3. Tekan `Enter` untuk simpan atau `Escape` untuk batal
4. Atau klik tombol ✓ dan ✕

### **5. Menyelesaikan/Menghapus Tugas**
- **Selesaikan**: Klik checkbox untuk marking selesai
- **Hapus**: Klik tombol trash, konfirmasi di modal

### **6. Keyboard Shortcuts**
- **`Ctrl/Cmd + K`**: Focus ke input tugas
- **`Escape`**: Tutup semua modal dan dropdown
- **`Enter`**: Simpan saat editing
- **`Escape`**: Batal saat editing

## 🔄 Lifecycle Livewire

### **Urutan Lifecycle Methods**
1. **`mount()`** - Inisialisasi komponen
2. **`boot()`** - Setup global state
3. **`hydrate()`** - Restore dari session
4. **`updating()`** - Sebelum property update
5. **`updated()`** - Setelah property update
6. **`rendering()`** - Sebelum render
7. **`rendered()`** - Setelah render
8. **`dehydrate()`** - Sebelum simpan session

### **Computed Properties**
```php
public function getTasksProperty()
{
    // Auto-refresh data tugas aktif
    return Task::with('tags')->active()->latest()->get();
}

public function getTagsProperty()
{
    // Auto-refresh data tag tersedia
    return Tag::orderBy('name')->get();
}
```

## 🎊 Sistem Event & Notifikasi

### **Event Handlers JavaScript**
- **`notify`** - Notifikasi hijau success
- **`notify-error`** - Notifikasi merah error
- **`task-created`** - Confetti + success message
- **`task-completed`** - Celebration message
- **`tag-created`** - Info notification
- **`tags-updated`** - Visual ring effect
- **`component-rendered`** - Auto-focus input
- **`modal-state-changed`** - Backdrop blur effect

### **Efek Visual**
- **Confetti**: Saat task dibuat berhasil
- **Toast Notifications**: Slide in/out smooth dari kanan
- **Ring Effect**: Highlight tombol tag saat dipilih
- **Modal Backdrop**: Blur background + disable scroll

## 🎨 Komponen UI

### **Form Components**
```blade
<!-- Input Field -->
<x-form.input
    wireModel="newTaskTitle"
    placeholder="Apa yang perlu dikerjakan?"
    :error="$errors->first('newTaskTitle')"
    required
/>

<!-- Button -->
<x-form.button
    wireClick="addTask"
    variant="success"
    :disabled="!$this->canAddTask()"
>
    ✨ Add Task
</x-form.button>

<!-- Priority Selector -->
<x-form.priority-selector
    :current-value="$newTaskPriority"
    wireClick="$set('newTaskPriority', '{value}')"
    required
/>
```

### **UI Modal**
```blade
<x-ui.modal
    :show="$showAddTagModal"
    title="Add New Tag"
    max-width="sm"
    on-close="cancelAddTag"
>
    <!-- Modal content -->
</x-ui.modal>
```

## 🧪 Testing & Debugging

### **Manual Testing**
1. **Buat Tugas**: Isi form dan submit
2. **Edit Tugas**: Klik judul, edit, save
3. **Kelola Tag**: Pilih tag, buat tag baru
4. **Complete/Delete**: Test checkbox dan delete
5. **Keyboard**: Test semua shortcuts

### **Console Debugging**
Buka DevTools Console untuk melihat:
```
✅ Success: Task created successfully!
📝 Task Created: 123
🏷️ Tags Updated: 2 tags selected
🔄 Component Rendered
```

### **Event Testing Manual**
```javascript
// Test di browser console:
Livewire.dispatch('notify', 'Test success');
Livewire.dispatch('notify-error', 'Test error');
```

## 🚀 Performance & Best Practices

### **Optimisasi Database**
- Index pada kolom yang sering di-query
- Eager loading untuk mencegah N+1 queries
- Proper foreign key constraints

### **Optimisasi Frontend**
- `wire:key` untuk loop performance
- Computed properties untuk data caching
- Minimal DOM manipulation

### **Security**
- Input validation dan sanitization
- CSRF protection
- SQL injection prevention via Eloquent

## 📚 Dokumentasi Tambahan

- **[PANDUAN_LIFECYCLE.md](PANDUAN_LIFECYCLE.md)** - Penjelasan lengkap Livewire lifecycle dan implementasi event system
- **[test-events.md](test-events.md)** - Panduan testing event handlers

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Aplikasi ini dibuat untuk tujuan edukasi dan pembelajaran Laravel TALL Stack.

## 🙏 Acknowledgments

- **Laravel Team** - Amazing PHP framework
- **Livewire Team** - Revolutionary reactive components
- **Tailwind CSS** - Beautiful utility-first CSS
- **Canvas Confetti** - Fun celebration effects

---

**Dibuat dengan ❤️ menggunakan Laravel TALL Stack**

> Aplikasi ini mendemonstrasikan implementasi modern Laravel dengan Livewire untuk pengalaman pengguna yang reaktif dan interaktif tanpa kompleksitas JavaScript frontend framework.