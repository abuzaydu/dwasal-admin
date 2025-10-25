<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Company;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admuser = User::create([
            'first_name' => 'Shabani',
            'last_name' => 'Mtaita',
            'phone' => '0762560460',
            'email' => 'admin@dwasal.com',
            'password' => bcrypt('Admin123'),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        $admuser->default_page = 'home';
        $admuser->save();

        $name = 'DODOMA WASHED SAND LIMITED';
        $company = new Company();
        $company->cuid = 'COM-'.$this->unique_code(16);
        $company->name = $name;
        $company->email = 'info@dwasal.co.tz';
        $company->mobile = '+255 758 458 004';
        $company->address = '41406 Mulebe, Msamalo, Chamwino District, P.O. Box 548 Dodoma, Tanzania';
        $company->save();


        $permissions = Permission::all();
        
        $role = new Role();
        $role->name = 'Admin_'.$company->id;
        $role->display_name = 'Admin';
        $role->description = 'Administrator';
        $role->save();

        $company->roles()->attach($role);
        foreach ($permissions as $key => $value) {
            $role->givePermissionTo($value);
        }

        $admuser->assignRole($role);

        $user = User::create([
            'first_name' => 'Omary',
            'last_name' => 'Njovu',
            'phone' => '0757333333',
            'email' => 'info@dwasal.co.tz',
            'password' => bcrypt('Admin123'),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        $user->default_page = 'home';
        $user->save();

        $company->users()->attach($admuser, ['is_default' => true]);
        $company->users()->attach($user, ['is_default' => true]);

        $user->assignRole($role);
    }

    
    private function unique_code($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }
}
