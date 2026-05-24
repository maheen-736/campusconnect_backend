<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
 
class SocietySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('societies')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
 
        $societies = [
            [
                'name' => 'ACM NUML',
                'description' => 'The ACM Student Chapter at NUML is the heartbeat of computer science culture on campus. We are a community of developers, designers, and tech enthusiasts who believe that technology should be built with purpose. From competitive programming marathons and hackathons to industry speaker sessions and open-source contribution drives, ACM NUML bridges the gap between classroom learning and real-world technology. Our members have gone on to work at top tech companies, launch startups, and contribute to global open-source projects. Whether you are writing your first line of code or shipping production software, ACM NUML is where you belong.',
                'tagline' => 'Code. Collaborate. Conquer.',
                'founded_at' => '2019',
            ],
            [
                'name' => 'NMS — NUML Media Society',
                'description' => 'The NUML Media Society is the creative powerhouse of the university. NMS is home to journalists, photographers, videographers, podcasters, graphic designers, and storytellers who believe that every story deserves to be told beautifully. We produce campus news, documentary films, photography exhibitions, and digital content that captures the spirit of student life at NUML. NMS trains the next generation of media professionals through hands-on workshops, industry visits, and live production projects. If you have a story inside you and a passion for visual communication, NMS is your stage.',
                'tagline' => 'Every Story Deserves to Be Told.',
                'founded_at' => '2020',
            ],
            [
                'name' => 'Entrepreneurship Society',
                'description' => 'The NUML Entrepreneurship Society exists to turn ideas into impact. We are a community of student founders, innovators, and business thinkers who are not waiting for graduation to start building. Through startup pitches, business case competitions, mentorship sessions with industry leaders, and our annual entrepreneurship summit, we create the ecosystem that university startups need to thrive. Our alumni have founded funded startups and led innovation programs across Pakistan. If you have an idea — no matter how early-stage — the Entrepreneurship Society will help you build it, pitch it, and grow it.',
                'tagline' => 'Build. Launch. Scale.',
                'founded_at' => '2021',
            ],
            [
                'name' => 'Psychology Society',
                'description' => 'The NUML Psychology Society is dedicated to building a campus where mental health is understood, respected, and prioritized. We organize awareness campaigns, peer support circles, guest lectures by clinical psychologists, and workshops on emotional intelligence, stress management, and mindfulness. Beyond mental health advocacy, we explore the fascinating science of human behavior — from cognitive biases to social psychology — through seminars, research discussions, and interactive experiments. The Psychology Society believes that understanding the human mind makes us better students, better professionals, and better human beings.',
                'tagline' => 'Understanding Minds. Building Empathy.',
                'founded_at' => '2020',
            ],
            [
                'name' => 'Literary Society',
                'description' => 'The NUML Literary Society is a sanctuary for readers, writers, poets, debaters, and orators. We celebrate the written and spoken word in all its forms — Urdu poetry mushairas, English debates, creative writing workshops, book clubs, essay competitions, and open-mic nights. The Literary Society believes that great communication is the foundation of every great career, and that literature is the mirror through which we understand our society. Whether you want to sharpen your public speaking, find your writing voice, or simply discuss great books with passionate readers, the Literary Society is your community.',
                'tagline' => 'Words Have Power. Use Them.',
                'founded_at' => '2018',
            ],
            [
                'name' => 'Sports Society',
                'description' => 'The NUML Sports Society champions athletic culture and the belief that a strong body powers a sharp mind. We organize inter-department tournaments, university-level competitions, fitness boot camps, and sports galas covering cricket, football, basketball, badminton, table tennis, and athletics. Beyond competition, the Sports Society promotes teamwork, discipline, and the spirit of sportsmanship that extends far beyond the playing field. We partner with the university administration to maintain world-class sports facilities and ensure every student has access to a healthy, active campus life.',
                'tagline' => 'Play Hard. Win Together.',
                'founded_at' => '2017',
            ],
        ];
 
        foreach ($societies as $society) {
            DB::table('societies')->insert([
                'name' => $society['name'],
                'description' => $society['description'],
                'tagline' => $society['tagline'],
                'founded_at' => $society['founded_at'],
                'cover_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
 