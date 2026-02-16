<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GeneratedReport;
use App\Jobs\GenerateReportJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReportGenerationTest extends TestCase
{
    /** @test */
    public function a_user_can_request_a_report()
    {
        $user = User::factory()->create(['last_name' => 'Test']);
        $this->actingAs($user);

        Queue::fake();

        $response = $this->post(route('reports.store'), [
            'title' => 'Test Report',
            'type' => 'user_detail',
            'format' => 'pdf',
            'start_date' => now()->subDays(7)->toDateString(),
            'end_date' => now()->toDateString(),
            'username' => 'testuser',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('generated_reports', [
            'user_id' => $user->id,
            'title' => 'Test Report',
            'type' => 'user_detail',
            'progress' => 0,
        ]);

        Queue::assertPushed(GenerateReportJob::class);
    }
}
