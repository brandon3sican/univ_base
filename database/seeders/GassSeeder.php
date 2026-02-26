<?php

namespace Database\Seeders;

use App\Models\Gass;
use Illuminate\Database\Seeder;

class GassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample programs
        $programs = [
            [
                'program_project_activity' => 'Environmental Protection and Conservation',
                'record_type' => 'program',
                'output_indicators' => 'Number of protected areas established, Environmental compliance rate',
                'office' => 'RO',
                'universe' => 5000,
                'baseline' => 3000,
                'accomplishment' => 3500,
                'target_2024' => 3800,
                'target_2025' => 4000,
                'target_2026' => 4200,
                'target_2027' => 4500,
                'target_2028' => 4800,
            ],
            [
                'program_project_activity' => 'Sustainable Forest Management',
                'record_type' => 'program',
                'output_indicators' => 'Hectares of forest land managed, Number of tree seedlings planted',
                'office' => 'RO',
                'universe' => 3000,
                'baseline' => 2000,
                'accomplishment' => 2200,
                'target_2024' => 2400,
                'target_2025' => 2600,
                'target_2026' => 2800,
                'target_2027' => 2900,
                'target_2028' => 3000,
            ],
            [
                'program_project_activity' => 'Biodiversity Conservation',
                'record_type' => 'program',
                'output_indicators' => 'Species conservation programs implemented, Habitat restoration areas',
                'office' => 'RO',
                'universe' => 2000,
                'baseline' => 1200,
                'accomplishment' => 1400,
                'target_2024' => 1500,
                'target_2025' => 1600,
                'target_2026' => 1700,
                'target_2027' => 1800,
                'target_2028' => 1900,
            ],
        ];

        $createdPrograms = [];
        foreach ($programs as $index => $programData) {
            $program = Gass::create(array_merge($programData, ['sort_order' => $index]));
            $createdPrograms[] = $program;
        }

        // Create sample projects for each program
        $projects = [
            ['name' => 'Watershed Management Project', 'parent_id' => $createdPrograms[0]->id],
            ['name' => 'Air Quality Monitoring Project', 'parent_id' => $createdPrograms[0]->id],
            ['name' => 'Community-Based Forest Management', 'parent_id' => $createdPrograms[1]->id],
            ['name' => 'Reforestation Initiative', 'parent_id' => $createdPrograms[1]->id],
            ['name' => 'Endangered Species Protection', 'parent_id' => $createdPrograms[2]->id],
            ['name' => 'Marine Conservation Project', 'parent_id' => $createdPrograms[2]->id],
        ];

        $createdProjects = [];
        foreach ($projects as $index => $projectData) {
            $project = Gass::create([
                'program_project_activity' => $projectData['name'],
                'record_type' => 'project',
                'output_indicators' => 'Project milestones completed, Stakeholders engaged',
                'office' => 'ABRA',
                'universe' => 500,
                'baseline' => 300,
                'accomplishment' => 350,
                'target_2024' => 380,
                'target_2025' => 400,
                'target_2026' => 420,
                'target_2027' => 450,
                'target_2028' => 480,
                'parent_id' => $projectData['parent_id'],
                'sort_order' => $index % 2, // Alternate between 0 and 1 for each parent
            ]);
            $createdProjects[] = $project;
        }

        // Create sample activities for some projects
        $activities = [
            ['name' => 'Conduct Watershed Assessment', 'parent_id' => $createdProjects[0]->id],
            ['name' => 'Implement Soil Conservation Measures', 'parent_id' => $createdProjects[0]->id],
            ['name' => 'Install Air Quality Monitors', 'parent_id' => $createdProjects[1]->id],
            ['name' => 'Community Training Programs', 'parent_id' => $createdProjects[2]->id],
            ['name' => 'Tree Planting Activities', 'parent_id' => $createdProjects[3]->id],
            ['name' => 'Species Population Survey', 'parent_id' => $createdProjects[4]->id],
        ];

        $createdActivities = [];
        foreach ($activities as $index => $activityData) {
            $activity = Gass::create([
                'program_project_activity' => $activityData['name'],
                'record_type' => 'activity',
                'output_indicators' => 'Activity reports submitted, Participants trained',
                'office' => 'BENGUET',
                'universe' => 100,
                'baseline' => 60,
                'accomplishment' => 70,
                'target_2024' => 75,
                'target_2025' => 80,
                'target_2026' => 85,
                'target_2027' => 90,
                'target_2028' => 95,
                'parent_id' => $activityData['parent_id'],
                'sort_order' => $index % 2,
            ]);
            $createdActivities[] = $activity;
        }

        // Create some sub-activities
        $subActivities = [
            ['name' => 'Data Collection and Analysis', 'parent_id' => $createdActivities[0]->id],
            ['name' => 'Stakeholder Consultation', 'parent_id' => $createdActivities[0]->id],
            ['name' => 'Monitoring Equipment Setup', 'parent_id' => $createdActivities[2]->id],
            ['name' => 'Training Material Development', 'parent_id' => $createdActivities[3]->id],
        ];

        foreach ($subActivities as $index => $subActivityData) {
            Gass::create([
                'program_project_activity' => $subActivityData['name'],
                'record_type' => 'sub_activity',
                'output_indicators' => 'Task completion reports, Deliverables submitted',
                'office' => 'IFUGAO',
                'universe' => 50,
                'baseline' => 30,
                'accomplishment' => 35,
                'target_2024' => 38,
                'target_2025' => 40,
                'target_2026' => 42,
                'target_2027' => 45,
                'target_2028' => 48,
                'parent_id' => $subActivityData['parent_id'],
                'sort_order' => $index,
            ]);
        }

        $this->command->info('GASS sample data created successfully!');
    }
}
