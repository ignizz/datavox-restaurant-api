<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->enum('user_type', User::USER_TYPES)->default(User::USER_WRITER);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('status')->default(User::STATUS_ACTIVE);
            $table->rememberToken();
            $table->timestamps();
        });

        User::create([
            'first_name' => 'Kareem',
            'last_name' => 'Lorenzana',
            'username' => 'kareem.lorenzana',
            'email' => 'iamkareempv@gmail.com',
            'user_type' => User::USER_ADMIN,
            'password' => Hash::make('Kareem.01@')
        ]);
        User::create([
            'first_name' => 'Gerardo',
            'last_name' => 'Mata',
            'username' => 'gbutters',
            'email' => 'gera90nike@gmail.com',
            'user_type' => User::USER_ADMIN,
            'password' => Hash::make('0987654321')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
