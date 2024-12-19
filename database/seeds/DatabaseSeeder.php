<?php
namespace Database\Seeds;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\BlogComment::factory(15)->create();

         $this->call([
            //  AdminRoleTable::class,
            //  AdminTable::class,
            //  SellerTableSeeder::class,
            
             BlogCommentSeeder::class,
         ]);
    }
}
