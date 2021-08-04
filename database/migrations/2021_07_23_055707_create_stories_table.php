<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('featured_image');
            $table->enum('story_card', array('video', 'image'));
            $table->integer('post_category');
            $table->integer('likes');
            $table->integer('view_count');
            $table->timestamps();
            $table->integer('story_tag');
            $table->enum('story_status', array('published', 'draft'));
            $table->integer('story_author');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories');
    }
}
