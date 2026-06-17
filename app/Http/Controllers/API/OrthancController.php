<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OrthancService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrthancController extends Controller
{
    public function __construct(private OrthancService $orthanc) {}

    public function studies(string $noRm, Request $request)
    {
        if (!$this->orthanc->isConfigured()) {
            return response()->json([
                'configured' => false,
                'studies'    => [],
            ]);
        }

        $from = $request->query('from');
        $to   = $request->query('to');
        $key  = "orthanc:studies:{$noRm}:" . md5((string) $from . '|' . (string) $to);

        $studies = Cache::remember($key, 60, fn () =>
            $this->orthanc->listStudiesForPatient($noRm, $from, $to)
        );

        return response()->json([
            'configured' => true,
            'studies'    => $studies,
        ]);
    }

    public function preview(string $instanceId)
    {
        abort_unless(preg_match('/^[a-f0-9-]{8,}$/i', $instanceId), 400, 'Instance ID tidak valid');

        $result = $this->orthanc->fetchPreview($instanceId);
        abort_unless($result, 404, 'Gambar tidak ditemukan');

        return response($result['body'], 200, [
            'Content-Type'  => $result['type'],
            'Cache-Control' => 'private, max-age=300',
            'X-Cache'       => $result['cached'] ? 'HIT' : 'MISS',
        ]);
    }

    public function dicom(string $instanceId)
    {
        abort_unless(preg_match('/^[a-f0-9-]{8,}$/i', $instanceId), 400, 'Instance ID tidak valid');

        $result = $this->orthanc->fetchDicom($instanceId);
        abort_unless($result, 404, 'File DICOM tidak ditemukan');

        return response($result['body'], 200, [
            'Content-Type'        => 'application/dicom',
            'Content-Disposition' => 'attachment; filename="' . $instanceId . '.dcm"',
            'X-Cache'             => $result['cached'] ? 'HIT' : 'MISS',
        ]);
    }

    public function archiveStudy(string $studyId)
    {
        abort_unless($this->orthanc->isConfigured(), 503, 'Orthanc belum dikonfigurasi');
        abort_unless(preg_match('/^[a-f0-9-]{8,}$/i', $studyId), 400, 'Study ID tidak valid');

        $result = $this->orthanc->archiveStudy($studyId);
        return response()->json($result);
    }
}
