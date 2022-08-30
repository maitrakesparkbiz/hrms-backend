<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NewsController extends Controller
{
    function saveNews(Request $request)
    {
        $data = $request->all();
        if (isset($data['status']) && $data['status'] == 'Publish') {
            $data['publish_date'] = new \DateTime();
        } else {
            $data['publish_date'] = null;
        }

        if ($request->id) {
            $news = $this->newsRepo->NewsOfId($request->id);
            $this->newsRepo->update($news, $data);
            $newsdata['news_id'] = $news;
            $newsid = $this->newsempRepo->getnewsEmp($news);
            foreach ($newsid as $key => $value) {
                $newsid[$key] = $value['id'];
            }
            $res = array_diff($newsid, $data['emp_id']);
            if ($res) {
                foreach ($res as $k => $v) {
                    $deletedata = $this->newsempRepo->deleteEmp($v);
                }
            }
            foreach ($data['emp_id'] as $key => $value) {

                $newsdata['emp_id'] = $value;
                $newsdata['emp_id'] = $this->userRepo->UserOfId($value);
                $already_exits = $this->newsempRepo->checkExists($newsdata);
                if ($already_exits) { } else {
                    $prepared_data = $this->newsempRepo->prepareData($newsdata);
                    $data = $this->newsempRepo->create($prepared_data);
                }
            }
            return response()->json("News Update succesfully");
        } else {
            $prepared_data = $this->newsRepo->prepareData($data);
            $news_id = $this->newsRepo->create($prepared_data);
            $newsdata['news_id'] = $this->newsRepo->NewsOfId($news_id);
            foreach ($data['emp_id'] as $key => $value) {
                $newsdata['emp_id'] = $this->userRepo->UserOfId($value);
                $prepared_data = $this->newsempRepo->prepareData($newsdata);
                $data = $this->newsempRepo->create($prepared_data);
            }
            return response()->json("News Add succesfully");
        }
    }

    function getAllNews(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $statusSearch = $request->columns[2]['search']['value'];
        $allRecords = $this->newsRepo->countAllNews();
        $filteredRecords = $this->newsRepo->countFilteredRows($statusSearch, $search);
        $data = $this->newsRepo->getAllNews($column_name, $order, $search, $start, $length, $statusSearch);
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRecords) > 0 ? (int)$filteredRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getNewsByID(Request $request)
    {
        return $this->newsRepo->getNewsById($request->id);
    }

    function deletenews(Request $request)
    {
        $newsid = $this->newsRepo->NewsOfId($request->id);
        $newsEmp = $this->newsempRepo->delete($newsid);
        $news = $this->newsRepo->delete($newsid);
        return response()->json("News Delete succesfully");
    }

    function publish_news(Request $request)
    {
        if ($request->id) {
            $data['publish_date'] = new \DateTime();
            $data['status'] = "Publish";
            $newsid = $this->newsRepo->NewsOfId($request->id);
            $res = $this->newsRepo->update($newsid, $data);
            return response()->json("News update succesfully");
        }
    }

    function getNewsByEmployee()
    {
        $emp_id = JWTAuth::toUser()->getId();
        $data = $this->newsempRepo->getNewsByEmployee($emp_id);
        if (isset($data)) {
            return response()->json($data);
        }
        return response()->json(null);
    }

}
