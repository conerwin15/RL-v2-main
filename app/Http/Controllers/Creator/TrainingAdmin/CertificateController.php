<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use PDF;
use App;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ Certificate };

class CertificateController extends Controller
{
    
    /**
    * Download PDF
    */

    public function previewPDF($id)
    {
        $user = Auth::user();
        $certificate = Certificate::findOrfail($id);
        if($certificate->is_master == true) {
            $pdf = PDF::loadView('creator.superadmin.certificate.master', ['learnername' => 'Learner Name', 'pathname' => 'Learning Path Name'])->setPaper('letter', 'landscape');
        } else {
            $pdf = PDF::loadView('creator.superadmin.certificate.pdf', compact('certificate'));
        }
        return $pdf->stream('certificate.pdf'); 
          
    }
}
