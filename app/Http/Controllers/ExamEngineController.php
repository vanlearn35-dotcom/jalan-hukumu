namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\Section;

class ExamEngineController extends Controller
{
    public function loadSection(Section $section)
    {
        abort_if($section->package->status !== 'published', 403);

        return response()->json([
            'section' => $section,
            'instruction' => $section->instruction,
            'passages' => $section->passages()->with('questions')->get(),
        ]);
    }
}