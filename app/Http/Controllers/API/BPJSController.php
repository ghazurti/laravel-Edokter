<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\BpjsTraits;

class BPJSController extends Controller
{
    use BpjsTraits;
    public function icare(Request $request)
    {
        $input = $request->all();
        $dokter = DB::table('maping_dokter_dpjpvclaim')
            ->where('kd_dokter', $input['kodedokter'])
            ->first();

        if (!$dokter || empty($dokter->kd_dokter_bpjs)) {
            \Log::warning('[ICare] Dokter belum mapping', ['kd_dokter' => $input['kodedokter']]);
            return response()->json([
                'code'    => 400,
                'message' => 'Dokter ' . $input['kodedokter'] . ' belum di-mapping ke kode dokter BPJS di tabel maping_dokter_dpjpvclaim',
            ]);
        }

        $data['param']      = $input['param'];
        $data['kodedokter'] = intval($dokter->kd_dokter_bpjs);

        \Log::info('[ICare] Request', [
            'url'  => rtrim(env('BPJS_ICARE_BASE_URL'), '/') . '/validate',
            'body' => $data,
        ]);

        $response = $this->requestPostBpjs('validate', $data);
        \Log::info('[ICare] Response', ['body' => $response->getContent()]);
        return $response;
    }
}
