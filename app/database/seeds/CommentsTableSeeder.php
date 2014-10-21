<?php
class CommentTableSeeder extends Seeder {
	public function run() {
		//DB::table ( 'comments' )->delete ();
		
		Comment::create ( array (
				'comment_post_ID' => '1',
				'comment_author' => 'test1',
				'comment_date' => date('Y-m-d H:i:s',time()),
				'comment_content'=>'Look I am a test comment.',
		) );
		
	}
}