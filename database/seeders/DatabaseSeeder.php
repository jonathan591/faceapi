<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie
use Spatie\Permission\Models\Permission; // Importa el modelo Permission de Spatie
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
   $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $viewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        // Crear algunos permisos si no existen
        // Estos permisos también aparecerán en la UI de Filament Shield.
        $createPostPermission = Permission::firstOrCreate(['name' => 'create posts', 'guard_name' => 'web']);
        $editPostPermission = Permission::firstOrCreate(['name' => 'edit posts', 'guard_name' => 'web']);
        $deletePostPermission = Permission::firstOrCreate(['name' => 'delete posts', 'guard_name' => 'web']);
        $viewUsersPermission = Permission::firstOrCreate(['name' => 'view users', 'guard_name' => 'web']);
        $manageUsersPermission = Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);

        // Asignar permisos a los roles.
        // Esto define qué permisos tiene cada rol por defecto.
        $superAdminRole->givePermissionTo(Permission::all()); // Opcional: darle todos los permisos al Super Admin

        $adminRole->givePermissionTo([
            $createPostPermission,
            $editPostPermission,
            $deletePostPermission,
            $viewUsersPermission,
            $manageUsersPermission,
        ]);

        $editorRole->givePermissionTo([
            $createPostPermission,
            $editPostPermission,
        ]);

        $viewerRole->givePermissionTo([
            $viewUsersPermission,
            // Agrega otros permisos de visualización si es necesario
        ]);


        // 2. Crear el usuario "admin"
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Busca por email para evitar duplicados si se ejecuta varias veces
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345'), // Siempre usa Hash::make() o bcrypt()
            ]
        );

        // 3. Asignar el rol al usuario
        // Aquí es donde asignas el rol que también gestionarás con Filament Shield.
        // Puedes asignar 'Super Admin' si quieres que tenga acceso total y se salte las políticas de permisos.
        $adminUser->assignRole($superAdminRole);
    }
}
