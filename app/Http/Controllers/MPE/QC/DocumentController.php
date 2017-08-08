<?php

namespace App\Http\Controllers\MPE\QC;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\MPE\QC\DocumentRepository;

class DocumentController extends Controller
{
    //
    private $doc;

    public function __construct(DocumentRepository $doc) {
        $this->doc = $doc;
    }

    public function getMateInfo()
    {

    }

    public function getBatchInfo()
    {

    }

    public function getDocument()
    {
        
    }
    
    public function testDocument()
    {
        $file_id = '5634D84DE0004E6DE050A8C0C96474C2';
        return $this->doc->getDownloadUrl($file_id);
    }
}
