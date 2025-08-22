<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Programming Languages
            ['name' => 'Python', 'category' => 'programming', 'proficiency_level' => 90, 'icon' => 'fab fa-python', 'is_featured' => true],
            ['name' => 'PHP', 'category' => 'programming', 'proficiency_level' => 85, 'icon' => 'fab fa-php', 'is_featured' => true],
            ['name' => 'JavaScript', 'category' => 'programming', 'proficiency_level' => 75, 'icon' => 'fab fa-js-square'],
            ['name' => 'Bash/Shell', 'category' => 'programming', 'proficiency_level' => 80, 'icon' => 'fas fa-terminal'],
            
            // Frameworks
            ['name' => 'Laravel', 'category' => 'framework', 'proficiency_level' => 90, 'icon' => 'fab fa-laravel', 'is_featured' => true],
            ['name' => 'Django', 'category' => 'framework', 'proficiency_level' => 85, 'icon' => 'fab fa-python'],
            ['name' => 'Yii Framework', 'category' => 'framework', 'proficiency_level' => 75, 'icon' => 'fas fa-code'],
            ['name' => 'WordPress', 'category' => 'framework', 'proficiency_level' => 80, 'icon' => 'fab fa-wordpress'],
            
            // DevOps & Cloud
            ['name' => 'AWS', 'category' => 'devops', 'proficiency_level' => 85, 'icon' => 'fab fa-aws', 'is_featured' => true],
            ['name' => 'Google Cloud Platform', 'category' => 'devops', 'proficiency_level' => 80, 'icon' => 'fab fa-google'],
            ['name' => 'Docker', 'category' => 'devops', 'proficiency_level' => 85, 'icon' => 'fab fa-docker', 'is_featured' => true],
            ['name' => 'Kubernetes', 'category' => 'devops', 'proficiency_level' => 75, 'icon' => 'fas fa-dharmachakra'],
            ['name' => 'Terraform', 'category' => 'devops', 'proficiency_level' => 80, 'icon' => 'fas fa-layer-group'],
            ['name' => 'Jenkins', 'category' => 'devops', 'proficiency_level' => 75, 'icon' => 'fas fa-cogs'],
            ['name' => 'GitLab CI/CD', 'category' => 'devops', 'proficiency_level' => 85, 'icon' => 'fab fa-gitlab'],
            ['name' => 'ArgoCD', 'category' => 'devops', 'proficiency_level' => 70, 'icon' => 'fas fa-sync-alt'],
            
            // Databases
            ['name' => 'MySQL', 'category' => 'database', 'proficiency_level' => 85, 'icon' => 'fas fa-database', 'is_featured' => true],
            ['name' => 'PostgreSQL', 'category' => 'database', 'proficiency_level' => 80, 'icon' => 'fas fa-database'],
            ['name' => 'Redis', 'category' => 'database', 'proficiency_level' => 75, 'icon' => 'fas fa-memory'],
            
            // Operating Systems
            ['name' => 'Linux (Ubuntu)', 'category' => 'system', 'proficiency_level' => 90, 'icon' => 'fab fa-ubuntu', 'is_featured' => true],
            ['name' => 'CentOS', 'category' => 'system', 'proficiency_level' => 80, 'icon' => 'fab fa-centos'],
            ['name' => 'Windows Server', 'category' => 'system', 'proficiency_level' => 70, 'icon' => 'fab fa-windows'],
            
            // Monitoring & Tools
            ['name' => 'Prometheus', 'category' => 'monitoring', 'proficiency_level' => 75, 'icon' => 'fas fa-chart-line'],
            ['name' => 'Grafana', 'category' => 'monitoring', 'proficiency_level' => 80, 'icon' => 'fas fa-chart-bar'],
            ['name' => 'SonarQube', 'category' => 'tools', 'proficiency_level' => 70, 'icon' => 'fas fa-bug'],
            ['name' => 'Git', 'category' => 'tools', 'proficiency_level' => 90, 'icon' => 'fab fa-git-alt'],
        ];

        foreach ($skills as $index => $skill) {
            Skill::firstOrCreate(
                ['name' => $skill['name'], 'category' => $skill['category']],
                array_merge($skill, [
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'description' => "Professional experience with {$skill['name']} in enterprise environments.",
                ])
            );
        }
    }
}
