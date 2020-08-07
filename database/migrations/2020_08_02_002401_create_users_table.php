<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstNames');
            $table->string('surname');
            $table->string('prefName');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phoneNumber')->unique();

            $table->unsignedBigInteger('city_id'); //FK
            $table->foreign('city_id')->references('id')->on('cities');

            $table->unsignedBigInteger('gender_id'); //FK
            $table->foreign('gender_id')->references('id')->on('genders');
            
            $table->unsignedBigInteger('marital_status_id'); //FK
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses');

            $table->date('dob');
            $table->integer('numOfChildren');
            $table->text('bio');
            $table->string('imageAddress')->nullable();

            $table->integer('prefMinAge');
            $table->integer('prefMaxAge');
            $table->integer('prefMaxNumOfChildren');

            $table->boolean('adminApproved')->default(0);
            $table->text('adminUnapprovedMessage')->nullable();
            $table->boolean('adminBanned')->default(0);
            $table->text('adminBannedMessage')->nullable();
            $table->boolean('deactivated')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
