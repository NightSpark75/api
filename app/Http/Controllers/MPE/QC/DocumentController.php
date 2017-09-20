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

    public function searchByBarcode()
    {
        $barcode = request()->input('barcode');
        $query = "
            select m.partno, m.ename, m.pname, h.batch, m.sds_no sds_no, h.coa_no coa_no, m.sfty
                ,(
                    select sum(amt) 
                    from mpe_house_e ee 
                    where ee.partno = h.partno and sta = 'N' and ee.batch = h.batch and ee.whouse = h.whouse
                        and ee.stor = h.stor and ee.grid = h.grid
                ) total 
            from mpe_house_m h, mpe_house_e e, mpe_mate m
            where h.code = '01' and h.code = e.code and h.partno = e.partno and h.batch = e.batch
                and h.whouse = e.whouse and h.stor = e.stor and h.grid = e.grid 
                and m.partno = h.partno and m.partno = e.partno and e.barcode = '$barcode'
        ";
        $result = $this->doc->getFileInfo($query);
        return $result;
    }

    public function searchByPartno()
    {
        $partno = request()->input('partno');
        $query = "
            select m.partno, m.ename, m.pname, '' batch, m.sds_no sds_no, '' coa_no, m.sfty
                ,(select sum(amt) from mpe_house_e e where e.partno = m.partno and sta = 'N') total 
            from mpe_mate m
            where m.partno = '$partno'
        ";
        $result = $this->doc->getFileInfo($query);
        return $result;
    }

    public function searchByBatch()
    {
        $batch = request()->input('batch');
        $query = "
            select m.partno, m.ename, m.pname, h.batch, m.sds_no sds_no, h.coa_no coa_no, m.sfty
                ,(
                    select sum(amt) 
                    from mpe_house_e ee 
                    where ee.partno = h.partno and sta = 'N' and ee.batch = h.batch and ee.whouse = h.whouse
                        and ee.stor = h.stor and ee.grid = h.grid
                ) total 
            from mpe_house_m h, mpe_mate m
            where h.code = '01' and m.partno = h.partno and h.batch = '$batch'
        ";
        $result = $this->doc->getFileInfo($query);
        return $result;
    }

    public function read($doc, $partno, $batch, $file_id)
    {
        $result = $this->doc->getFileSecurity($doc, $partno, $batch, $file_id);
        return $result;
    }
}
