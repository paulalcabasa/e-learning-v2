<?php

namespace App\Http\Controllers;

use PDF;

class PDFController extends Controller
{
    public function download() {

    	$pdf = PDF::loadView('pdfs.consent-form');
      	//return $pdf;

        return $pdf->stream();
    }
}
