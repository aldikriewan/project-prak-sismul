## Panduan Pembuatan CRUD di Laravel

### 1. Membuat Model, Migration, dan Controller

1. Membuat Model, Migration, dan Controller Sekaligus
   Jalankan perintah berikut untuk membuat model, migration, dan controller:

    ```bash
    php artisan make:model namamenu -mcr
    ```

2. Edit File Migration
   Buka file migration yang telah dibuat di folder database/migrations dan tambahkan schema berikut di fungsi up:

    ```bash
    public function up()
     {
         Schema::create('namamenus', function (Blueprint $table) {
             $table->id();
             $table->string('judul')->nullable();
             $table->text('deskripsi')->nullable();
             $table->string('gambar')->nullable();
             $table->timestamps();
         });
     }
    ```

3. Migrasi Schema ke Database
   Jalankan perintah berikut untuk menerapkan schema ke database:

    ```bash
    php artisan migrate
    ```

4. Tambahkan Properti di Model
   Edit file app/Models/NamaMenu.php untuk menambahkan properti $fillable:

    ```bash
    <?php

     namespace App\Models;

     use Illuminate\Database\Eloquent\Factories\HasFactory;
     use Illuminate\Database\Eloquent\Model;

     class namamenu extends Model
     {
         use HasFactory;

         protected $fillable = ['judul', 'deskripsi', 'gambar'];
     }
    ```

5. Implementasi CRUD di Controller
   Edit file controller app/Http/Controllers/NamaMenuController.php dan tambahkan fungsi CRUD berikut:

    ```bash
     <?php

     namespace App\Http\Controllers;

     use Illuminate\Http\Request;
     use App\Models\NamaMenu;

     class NamaMenuController extends Controller
     {
         // GET - Menampilkan daftar namamenu
         public function index()
         {
             $namamenus = NamaMenu::all();
             return response()->json([
                 'success' => true,
                 'data' => $namamenus
             ]);
         }

         // GET per ID - Menampilkan detail namamenu
         public function show($id)
         {
             $namamenu = NamaMenu::find($id);

             if (!$namamenu) {
                 return response()->json([
                     'success' => false,
                     'message' => 'NamaMenu not found.'
                 ], 404);
             }

             return response()->json([
                 'success' => true,
                 'data' => $namamenu
             ]);
         }

         // POST - Menyimpan namamenu baru
         public function store(Request $request)
         {
             $request->validate([
                 'judul' => 'required|string|max:255',
                 'deskripsi' => 'nullable|string',
                 'gambar' => 'nullable|image|max:2048',
             ]);

             $imagePath = $request->file('gambar')
                 ? $request->file('gambar')->store('images', 'public')
                 : null;

             $namamenu = NamaMenu::create([
                 'judul' => $request->judul,
                 'deskripsi' => $request->deskripsi,
                 'gambar' => $imagePath,
             ]);

             return response()->json([
                 'success' => true,
                 'message' => 'NamaMenu berhasil ditambahkan.',
                 'data' => $namamenu
             ]);
         }

         // PUT - Menyimpan perubahan pada namamenu
         public function update(Request $request, $id)
         {
             $request->validate([
                 'judul' => 'required|string|max:255',
                 'deskripsi' => 'nullable|string',
                 'gambar' => 'nullable|image|max:2048',
             ]);

             $namamenu = NamaMenu::find($id);

             if (!$namamenu) {
                 return response()->json([
                     'success' => false,
                     'message' => 'NamaMenu not found.'
                 ], 404);
             }

             if ($request->file('gambar')) {
                 $imagePath = $request->file('gambar')->store('images', 'public');
                 $namamenu->gambar = $imagePath;
             }

             $namamenu->update([
                 'judul' => $request->judul,
                 'deskripsi' => $request->deskripsi,
             ]);

             return response()->json([
                 'success' => true,
                 'message' => 'NamaMenu berhasil diperbarui.',
                 'data' => $namamenu
             ]);
         }

         // DELETE - Menghapus namamenu
         public function destroy($id)
         {
             $namamenu = NamaMenu::find($id);

             if (!$namamenu) {
                 return response()->json([
                     'success' => false,
                     'message' => 'NamaMenu not found.'
                 ], 404);
             }

             $namamenu->delete();

             return response()->json([
                 'success' => true,
                 'message' => 'NamaMenu berhasil dihapus.'
             ]);
         }
     }
    ```

6. Hubungkan Controller ke Route
   Tambahkan route berikut di file routes/api.php:

    ```bash
    use App\Http\Controllers\NamaMenuController;

    Route::resource('namamenu', NamaMenuController::class);
    ```

### 2. Membuat Seeder

1. Membuat Seeder
   Jalankan perintah berikut untuk membuat seeder:

    ```bash
    php artisan make:seeder NamaMenuSeeder
    ```

2. Isi Seeder

    ```bash
    <?php

     namespace Database\Seeders;

     use Illuminate\Database\Console\Seeds\WithoutModelEvents;
     use Illuminate\Support\Facades\DB;
     use Illuminate\Database\Seeder;

     class NamaMenuSeeder extends Seeder
     {
         /**
          * Run the database seeds.
          *
          * @return void
          */
         public function run()
         {
             DB::table('namamenus')->insert([
                 'judul' => 'Contoh NamaMenu',
                 'deskripsi' => 'Ini adalah deskripsi contoh namamenu.',
                 'gambar' => null,
                 'created_at' => now(),
                 'updated_at' => now(),
             ]);
         }
     }
    ```

3. Jalankan Seeder
   Jalankan perintah berikut untuk menambahkan data ke tabel:
    ```bash
    php artisan db:seed --class=NamaMenuSeeder
    ```
"# crude_story" 
