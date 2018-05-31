<?php
/**
 * QC文件資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/08/08
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPE\QC;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class DocumentRepository
 *
 * @package App\Repositories\MPE\QC
 */
class DocumentRepository
{   
    use Sqlexecute;
    
    public function __construct() {

    }

    public function getInfo($search)
    {
        try {
            $info = DB::select("
            select e.partno, e.batch, m.ename, pk_mpe.fu_storn(e.whouse, e.stor) storn, 
                    pk_mpe.fu_get_count_qty2(e.code, e.partno, e.batch, e.whouse, e.stor, e.grid)||m.unit qty, 
                    m.sfty||m.unit sfty, pk_mpe.fu_get_qc_coa(e.partno, e.batch) coa_no, m.sds_no, 
                    m.pname, m.molef, m.molew, m.casno, m.lev, m.conc, m.scrap, m.sfty, m.toxicizer, m.hazard, m.reagent, m.pioneer, m.store_type
                from mpe_house_e e, mpe_mate m
                where e.code = '01' and e.code = m.code and e.partno = m.partno
                    and (e.partno like '%$search%' 
                        or e.batch like '%$search%' 
                        or UPPER(m.ename) like UPPER('%$search%')
                        or UPPER(m.pname) like UPPER('%$search%')
                        or UPPER(m.reagent) like UPPER('%$search%')
                        or m.casno like '%$search%'
                        or m.molef like '%$search%'
                        or e.barcode = '$search')
                group by e.code, e.partno, e.batch, m.ename, m.unit, m.sfty, e.whouse, e.stor, e.grid,
                    m.sds_no, m.pname, m.molef, m.molew, m.casno, m.lev, 
                    m.conc, m.scrap, m.sfty, m.toxicizer, m.hazard, m.reagent, m.pioneer, m.store_type
                union
                    select m.partno, null batch, m.ename, '' storn, '0' qty, m.sfty||m.unit sfty, null coa_no, m.sds_no, 
                            m.pname, m.molef, m.molew, m.casno, m.lev, m.conc, m.scrap, m.sfty, m.toxicizer, m.hazard, m.reagent, m.pioneer, m.store_type
                        from mpe_mate m
                        where m.code = '01'
                            and (m.partno like '%$search%' 
                                or UPPER(m.ename) like UPPER('%$search%')
                                or UPPER(m.pname) like UPPER('%$search%')
                                or UPPER(m.reagent) like UPPER('%$search%')
                                or m.casno like '%$search%'
                                or m.molef like '%$search%')
                            and not exists(
                                select *
                                from mpe_house_m hh
                                where hh.partno = m.partno
                            )
                order by 1, 2 desc
            ");
            if (count($info) === 0) {
                $info = DB::select("
                    select m.partno, null batch, m.ename, '' storn, 0 qty, m.sfty||m.unit sfty, null coa_no, m.sds_no
                    from mpe_mate m
                    where m.code = '01'
                        and (m.partno like '%$search%' 
                        or UPPER(m.ename) like UPPER('%$search%')
                        or UPPER(m.pname) like UPPER('%$search%')
                        or UPPER(m.reagent) like UPPER('%$search%')
                        or m.casno like '%$search%'
                        or m.molef like '%$search%')
                    order by m.partno
                ");
            }
            return $this->success([
                'msg' => '查詢成功',
                'info' => $info,
            ]);
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function getFileInfo($query)
    {
        try {
            $info = DB::selectOne($query);
            if ($info === null) {
                throw new Exception('查詢不到資料!');
            }
            $result = [
                'result' => true,
                'msg' => '取得檔案資訊成功!(#0001)',
                'info' => $info,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function getFileSecurity($doc, $partno, $batch, $file_id)
    {
        switch ($doc) {
            case 'sds':
                $query = "
                    select count(*) res, sds_no file_id
                    from mpe_mate
                    where partno = '$partno' and sds_no = '$file_id'
                    group by sds_no
                ";
                return $this->checkFile($query);
            case 'coa':
                $query = "
                    select count(*) res, coa_no file_id
                    from mpe_house_m
                    where partno = '$partno' and batch = '$batch' and coa_no = '$file_id'
                    group by coa_no
                ";
                return $this->checkFile($query);
            default:
                return $this->errorPage('文件類型錯誤!');
        }
    }

    private function checkFile($query)
    {
        try{
            $check = DB::selectOne($query);

            if ($check->res === '1') {
                return $this->pdfToCanvas($check->file_id);
            }

            throw new Exception('找不到文件資訊!');
        } catch (Exception $e) {
            return $this->errorPage($e->getMessage());
        }
        
    }

    private function pdfToCanvas($file_id)
    {
        try {
            $file = DB::selectOne("
                select *
                from api_file_code
                where file_id = '$file_id'
            ");
            $code = $file->code;
            $mime = $file->mime;
            $src = "data:$mime;base64,$code";
            return view('service.pdfcanvas')->with('src', $src);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        
    }

    public function barcodeList($partno, $batch)
    {
        $list = DB::select("
            select barcode, partno, batch, opdate, opvl, buydate, valid, sta
                from mpe_house_e
                where partno = '$partno' and batch= '$batch' and code = '01'
        ");
        return $list;
    }
}   