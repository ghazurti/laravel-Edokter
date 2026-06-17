<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class OrthancService
{
    private string $baseUrl;
    private string $user;
    private string $pass;
    private int $timeout;
    private string $archiveRoot;

    public function __construct()
    {
        $url  = rtrim((string) env('ORTHANC_URL', ''), '/');
        $port = trim((string) env('ORTHANC_PORT', ''));
        $this->baseUrl = $port !== '' ? "{$url}:{$port}" : $url;
        $this->user    = (string) env('ORTHANC_USER', '');
        $this->pass    = (string) env('ORTHANC_PASS', '');
        $this->timeout = (int) env('ORTHANC_TIMEOUT', 15);
        $custom = trim((string) env('ORTHANC_ARCHIVE_PATH', ''));
        $this->archiveRoot = $custom !== '' ? rtrim($custom, '/\\') : storage_path('app/orthanc');
    }

    public function archivePath(string $instanceId, string $ext): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9\-]/', '', $instanceId);
        $sub  = substr($safe, 0, 2) ?: '00';
        $dir  = $this->archiveRoot . DIRECTORY_SEPARATOR . $sub;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir . DIRECTORY_SEPARATOR . $safe . '.' . ltrim($ext, '.');
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->user !== '';
    }

    private function http()
    {
        return Http::withBasicAuth($this->user, $this->pass)
            ->timeout($this->timeout);
    }

    public function findStudies(string $noRm, ?string $tglAwal = null, ?string $tglAkhir = null): array
    {
        $studyDate = '';
        if ($tglAwal && $tglAkhir) {
            $studyDate = $this->toDicomDate($tglAwal) . '-' . $this->toDicomDate($tglAkhir);
        } elseif ($tglAwal) {
            $studyDate = $this->toDicomDate($tglAwal);
        }

        $query = ['PatientID' => $noRm];
        if ($studyDate !== '') {
            $query['StudyDate'] = $studyDate;
        }

        $response = $this->http()->post($this->baseUrl . '/tools/find', [
            'Level'  => 'Study',
            'Expand' => true,
            'Query'  => $query,
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }

    public function getSeries(string $seriesId): ?array
    {
        $response = $this->http()->get($this->baseUrl . '/series/' . $seriesId);
        return $response->successful() ? $response->json() : null;
    }

    public function getStudy(string $studyId): ?array
    {
        $response = $this->http()->get($this->baseUrl . '/studies/' . $studyId);
        return $response->successful() ? $response->json() : null;
    }

    public function fetchPreview(string $instanceId): ?array
    {
        $path = $this->archivePath($instanceId, 'jpg');
        if (is_file($path) && filesize($path) > 0) {
            return ['body' => file_get_contents($path), 'type' => 'image/jpeg', 'cached' => true];
        }
        if (!$this->isConfigured()) {
            return null;
        }
        $response = $this->http()
            ->withHeaders(['Accept' => 'image/jpeg'])
            ->get($this->baseUrl . '/instances/' . $instanceId . '/preview');
        if (!$response->successful()) {
            return null;
        }
        @file_put_contents($path, $response->body());
        return ['body' => $response->body(), 'type' => 'image/jpeg', 'cached' => false];
    }

    public function fetchDicom(string $instanceId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }
        $response = $this->http()->get($this->baseUrl . '/instances/' . $instanceId . '/file');
        if (!$response->successful()) {
            return null;
        }
        return ['body' => $response->body(), 'cached' => false];
    }

    public function archiveStudy(string $studyId): array
    {
        $study = $this->getStudy($studyId);
        if (!$study) return ['ok' => false, 'instances' => 0];
        $count = 0;
        foreach ($study['Series'] ?? [] as $seriesId) {
            $s = $this->getSeries($seriesId);
            foreach (($s['Instances'] ?? []) as $iid) {
                if ($this->fetchPreview($iid) !== null) $count++;
            }
        }
        return ['ok' => true, 'instances' => $count];
    }

    public function listStudiesForPatient(string $noRm, ?string $tglAwal = null, ?string $tglAkhir = null): array
    {
        $studies = $this->findStudies($noRm, $tglAwal, $tglAkhir);
        $result  = [];

        foreach ($studies as $study) {
            $studyId   = $study['ID'] ?? null;
            $tags      = $study['MainDicomTags'] ?? [];
            $pTags     = $study['PatientMainDicomTags'] ?? [];
            $seriesIds = $study['Series'] ?? [];
            $series    = [];

            foreach ($seriesIds as $sid) {
                $s = $this->getSeries($sid);
                if (!$s) continue;
                $series[] = [
                    'id'          => $s['ID'] ?? $sid,
                    'modality'    => $s['MainDicomTags']['Modality'] ?? '',
                    'description' => $s['MainDicomTags']['SeriesDescription'] ?? '',
                    'instances'   => $s['Instances'] ?? [],
                ];
            }

            $result[] = [
                'id'             => $studyId,
                'study_date'     => $tags['StudyDate'] ?? '',
                'study_time'     => $tags['StudyTime'] ?? '',
                'description'    => $tags['StudyDescription'] ?? '',
                'accession'      => $tags['AccessionNumber'] ?? '',
                'patient_id'     => $pTags['PatientID'] ?? '',
                'patient_name'   => $pTags['PatientName'] ?? '',
                'series'         => $series,
            ];
        }

        return $result;
    }

    private function toDicomDate(string $date): string
    {
        $clean = preg_replace('/\D/', '', $date);
        if (strlen($clean) === 8) {
            return $clean;
        }
        $ts = strtotime($date);
        return $ts ? date('Ymd', $ts) : '';
    }
}
