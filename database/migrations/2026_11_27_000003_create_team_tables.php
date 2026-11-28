<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Admin > Team. The team used to be fixed in config/team.php; it now lives in the database so the admin can edit
 * it. A section (e.g. "Software Engineer") has a cover picture shown on its cards; each member belongs to one
 * section. The team as it was in config/team.php is copied in, so the Team page looks the same after migrating.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('cover')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_group_id')->constrained('team_groups')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('designation', 150)->nullable();
            $table->string('skills', 400)->nullable(); // comma separated, shown as chips
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('experience', 60)->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['team_group_id', 'sort_order']);
        });

        $now = now();
        foreach ($this->team() as $g => [$name, $cover, $members]) {
            $groupId = DB::table('team_groups')->insertGetId(['name' => $name, 'cover' => $cover, 'sort_order' => $g + 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
            foreach ($members as $i => $m) {
                DB::table('team_members')->insert($m + ['team_group_id' => $groupId, 'sort_order' => $i + 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('team_groups');
    }

    /** The team from config/team.php on 27 Nov 2026. */
    private function team(): array
    {
        $antu = ['name' => 'Antu Debnath', 'skills' => 'Security Strategy, Penetration Testing, Risk Assessment', 'photo' => 'assets/img/team/antu.webp',
            'github_url' => 'https://github.com/antudebnath-sec', 'portfolio_url' => 'https://antudebnath-sec.github.io/'];

        return [
            ['Author & Management Team', 'assets/img/team/covers/author.webp', [
                ['name' => 'Adnan Sirat', 'designation' => 'CEO', 'skills' => 'Leadership, Strategy, Client Relations'],
                ['name' => 'Saiful', 'designation' => 'Team Lead', 'skills' => 'Team Management, Project Planning, Agile'],
                ['designation' => 'HR'] + $antu,
            ]],
            ['Software Engineer', 'assets/img/team/covers/software.webp', [
                ['name' => 'Kaucher', 'designation' => 'Full Stack Software Engineer', 'skills' => 'PHP, Laravel, Next.js, React, MySQL, REST APIs',
                    'photo' => 'assets/img/team/kaucher.webp', 'github_url' => 'https://github.com/smkaucher', 'portfolio_url' => 'https://smkaucher.github.io/'],
                ['name' => 'Antor', 'designation' => 'Full Stack Software Engineer & Android Developer', 'skills' => 'Next.js, NestJS, React, Flutter, Java, Android',
                    'photo' => 'assets/img/team/antor.webp', 'github_url' => 'https://github.com/AntorPi314/', 'portfolio_url' => 'https://antor.is-a.dev/',
                    'linkedin_url' => 'https://www.linkedin.com/in/antor-hawlader'],
                ['name' => 'Tamim', 'designation' => 'Frontend Engineer & Flutter Developer', 'skills' => 'JavaScript, React, Flutter, UI Implementation',
                    'photo' => 'assets/img/team/tamim.webp', 'github_url' => 'https://github.com/tamiie56', 'portfolio_url' => 'https://tamiie56.github.io/portfolio/',
                    'linkedin_url' => 'https://www.linkedin.com/in/tamim-chad-likhon-206709389/'],
            ]],
            ['Security Engineer Team', 'assets/img/team/covers/security.webp', [
                ['designation' => 'Security Specialist & Lead'] + $antu,
                ['name' => 'Pranto Kumar Shil', 'designation' => 'Security Engineer', 'skills' => 'Vulnerability Assessment, Network Security, Incident Response',
                    'portfolio_url' => 'https://pran0x.dev/'],
                ['name' => 'Siam Shikder Nahid', 'designation' => 'Security Engineer', 'skills' => 'Secure Code Review, OWASP, Threat Analysis'],
            ]],
            ['Marketing & Designer Team', 'assets/img/team/covers/marketing.webp', [
                ['name' => 'Shuvashish Biswas Shuvo', 'designation' => 'Digital Marketer', 'skills' => 'SEO, Google Ads, Meta Ads, Analytics', 'photo' => 'assets/img/team/shuvo.webp',
                    'portfolio_url' => 'https://shuvashish-biswas-shuvo-digital-marketer.vercel.app/', 'linkedin_url' => 'https://bd.linkedin.com/in/shuvashish-biswas'],
                ['name' => 'Ruhul Amin', 'designation' => 'Social Media Analyst', 'skills' => 'Social Media Analytics, Content Strategy, Reporting', 'photo' => 'assets/img/team/ruhul-amin.webp'],
                ['name' => 'Saeed Ben Bishal', 'designation' => 'Graphics Designer', 'skills' => 'UI/UX Design, Graphic Design, Branding, Figma', 'photo' => 'assets/img/team/bishal.webp'],
            ]],
        ];
    }
};
