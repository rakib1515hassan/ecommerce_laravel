<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogCommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blog_comments')->insert([
            'comment' => Str::random(10),
            'is_approved' => '1',
            'blog_id' => '1',
            'created_by_id' => '1',
            'created_at'=>now(),
            'updated_at'=>now()
        ]);
    }
}
