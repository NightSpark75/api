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
                select hm.partno, hm.batch, m.ename, pk_mpe.fu_storn(hm.whouse, hm.stor) storn, hm.qty||m.unit qty, m.sfty||m.unit sfty, hm.coa_no, m.sds_no
                from mpe_house_m hm, mpe_house_e he, mpe_mate m
                where (hm.partno like '%$search%' 
                        or hm.batch like '%$search%' 
                        or UPPER(m.ename) like UPPER('%$search%')
                        or UPPER(m.pname) like UPPER('%$search%')
                        or UPPER(m.reagent) like UPPER('%$search%')
                        or m.casno like '%$search%'
                        or m.molef like '%$search%'
                        or he.barcode = '$search')
                    and m.code = '01' and hm.code = '01' and he.code = '01'
                    and hm.partno = he.partno and hm.batch = he.batch and hm.whouse = he.whouse and hm.stor = he.stor and hm.grid = he.grid
                    and hm.partno = m.partno
                group by hm.partno, hm.batch, m.ename, hm.whouse, hm.stor, hm.qty, m.sfty, hm.coa_no, m.sds_no, m.unit
                union
                select m.partno, null batch, m.ename, '' storn, '0' qty, m.sfty||m.unit sfty, null coa_no, m.sds_no
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
                order by 1
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
            //return view('service.pdfcanvas')->with('src', $src);
            $response = 
            response(base64_decode($file->code))
            ->header('Content-Type', $mime) // MIME
            ->header('Content-length', strlen($code)) // base64
            ->header('Content-Transfer-Encoding', 'binary');
            return $response;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        
    }
}   