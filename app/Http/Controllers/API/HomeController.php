<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function show($name){
        $tasks = DB::table("MASTERS as m")
            ->leftJoin("TASKV as tv","m.ID", "tv.MASTER_ID")
            ->select("tv.TASKNAME", "tv.ID")
            ->where("m.NAME",$name)
            ->distinct()
            ->get();
        $data = [];
        foreach ($tasks as $item){
            $data[$item->TASKNAME] = new \stdClass();
            $data[$item->TASKNAME]->slave=implode( DB::table("TASKV as tv")
                ->select("USERNAME")
                ->where("TASKNAME", $item->TASKNAME)
                ->distinct()
                ->get()
                ->pluck("USERNAME")->all(),", ");
            $data[$item->TASKNAME]->files=DB::table("TASKCONTENT as tc")
                ->select("RELATIVEPATH", "CONTENT", "ID")
                ->where("TASKNAME", $item->TASKNAME)
                ->get()
                ->transform(function($parr){
                    $parr->CONTENT=$this->convert_from_latin1_to_utf8_recursively($parr->CONTENT);
                    return $parr;
                });
            $data[$item->TASKNAME]->ID=$item->ID;
        }
        return response()->json(compact("data"));
    }
    public function download($id){
        $document=DB::table("TASKCONTENT as tc")->where("ID",$id)->first();
        $document->CONTENT=$this->convert_from_latin1_to_utf8_recursively($document->CONTENT);
        return response()->json(compact("document"));

//        $file_contents = base64_decode($document->CONTENT);
//        return response($file_contents)
//            ->header('Cache-Control', 'no-cache private')
//            ->header('Content-Description', 'File Transfer')
//            ->header('Content-Type',"text/*")
//            ->header('Content-length', strlen($file_contents))
//            ->header('Content-Disposition', 'attachment; filename=' . $document->file_name)
//            ->header('Content-Transfer-Encoding', 'binary');
    }
    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) $ret[ $i ] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
    }
}
