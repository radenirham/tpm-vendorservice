<?php

namespace App\Http\Controllers\v1\Sample;

use App\Http\Controllers\v1\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Base\Samples;
use App\Models\Base\ViewSamples;
use App\Models\Base\Categories;

class CrudController extends ApiController
{

    public function allList(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'get');

        $search = isset($this->param['search']) ? $this->param['search'] : '';
        $limit = isset($this->param['limit']) ? $this->param['limit'] : 10;
        $offset = isset($this->param['offset']) ? $this->param['offset'] : 0;
        $order = isset($this->param['order']) ? $this->param['order'] : 'sample_id';
        $sort = isset($this->param['sort']) ? $this->param['sort'] : 'asc';
        $status = isset($this->param['status']) ? $this->param['status'] : '1';
        $field = isset($this->param['field']) ? $this->param['field'] : 'all';


        $query = ViewSamples::where('sample_status', $status)
            ->where(function ($query) use ($search, $field) {
                if($field == 'all') {
                    $query
                        ->orWhereRaw("lower(sample_code) like '%" . strtolower($search) . "%'")
                        ->orWhereRaw("lower(sample_text) like '%" . strtolower($search) . "%'")
                        ->orWhereRaw("lower(sample_date::text) like '%" . strtolower($search) . "%'")
                        ->orWhereRaw("lower(sample_datetime::text) like '%" . strtolower($search) . "%'")
                        ->orWhereRaw("lower(sample_number::text) like '%" . strtolower($search) . "%'")
                        ->orWhereRaw("lower(sample_category_name::text) like '%" . strtolower($search) . "%'");
                } else {
                    $query->whereRaw("lower(".$field."::text) like '%" . strtolower($search) . "%'");
                }
            });

        $data['total'] = $query->count();

        $data['result'] = $query->select(
                'sample_uuid',
                'sample_code',
                'sample_text',
                'sample_date',
                'sample_datetime',
                'sample_number',
                'sample_category_uuid',
                'sample_category_name',
                'sample_file_path'
            )
            ->orderBy($order, $sort)
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $this->output(
            true,
            '',
            $data
        );
    }

    public function category(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'get');

        $data = Categories::where('category_status', 1)
            ->select(
                'category_uuid',
                'category_name'
            )
            ->get();

        return $this->output(
            true,
            '',
            $data
        );
    }

    public function create(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'post');

        $uuid = $this->uuid();

        $code = isset($this->param['sample_code']) ? $this->param['sample_code'] : '';
        $text = isset($this->param['sample_text']) ? $this->param['sample_text'] : '';
        $date = isset($this->param['sample_date']) ? $this->param['sample_date'] : '';
        $datetime = isset($this->param['sample_datetime']) ? $this->param['sample_datetime'] : '';
        $category = isset($this->param['sample_category']) ? $this->param['sample_category'] : '';
        $file = isset($this->param['sample_file']) ? $this->param['sample_file'] : array();

        $sample = new Samples;
        $sample->sample_uuid = $uuid;
        $sample->sample_code = $code;
        $sample->sample_text = $text;
        $sample->sample_date = $date;
        $sample->sample_datetime = $datetime;
        $sample->sample_number = 0;
        $sample->sample_category_uuid = $category;
        $sample->sample_file_name = $file[0]['original'];
        $sample->sample_file_path = $file[0]['path'];
        $sample->sample_file_size = $file[0]['size'];
        $sample->sample_file_mime = $file[0]['mime'];
        $sample->sample_create_by = $this->userdata->employeeUuid;
        $sample->sample_create_date = date('Y-m-d H:i:s', time());;
        $sample->sample_status = 1;

        DB::beginTransaction();
        try {
            $sample->save();
            $data = $sample;
            DB::commit();

            return $this->output(
                true,
                'Data sample baru berhasil disimpan',
                $data
            );

        } catch (\Throwable $th) {
            DB::rollback();

            return $this->output(
                false,
                'Data sample baru gagal disimpan',
                $data
            );
        }
    }

    public function detail(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'get');

        $uuid = isset($this->param['uuid']) ? $this->param['uuid'] : '';

        $data = ViewSamples::where('sample_uuid', $uuid)
            ->first();

        return $this->output(
            true,
            '',
            $data
        );
    }

    public function inactive(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'post');

        $uuid = isset($this->param['uuid']) ? $this->param['uuid'] : '';


        DB::beginTransaction();
        try {
            // simpan log data
            Samples::insert(Samples::select(
                    'sample_uuid',
                    'sample_code',
                    'sample_text',
                    'sample_date',
                    'sample_datetime',
                    'sample_number',
                    'sample_file_name',
                    'sample_file_path',
                    'sample_file_size',
                    'sample_file_mime',
                    'sample_category_uuid',
                    'sample_create_by',
                    'sample_create_date',
                    'sample_status',
                    'sample_uuid as sample_log_uuid'
                )
                ->where('sample_log_uuid', null)
                ->where('sample_uuid', $uuid)
                ->first()
                ->toArray());

            // proses update data
            Samples::where('sample_uuid', $uuid)
                ->where('sample_log_uuid', null)
                ->update([
                    'sample_status' => 0
                ]);
            DB::commit();

            return $this->output(
                true,
                'Data sample berhasil dihapus',
                $data
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->output(
                false,
                'Data sample gagal dihapus',
                $data
            );
        }
    }

    public function reactive(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'post');

        $uuid = isset($this->param['uuid']) ? $this->param['uuid'] : '';


        DB::beginTransaction();
        try {
            // simpan log data
            Samples::insert(Samples::select(
                    'sample_uuid',
                    'sample_code',
                    'sample_text',
                    'sample_date',
                    'sample_datetime',
                    'sample_number',
                    'sample_file_name',
                    'sample_file_path',
                    'sample_file_size',
                    'sample_file_mime',
                    'sample_category_uuid',
                    'sample_create_by',
                    'sample_create_date',
                    'sample_status',
                    'sample_uuid as sample_log_uuid'
                )
                ->where('sample_log_uuid', null)
                ->where('sample_uuid', $uuid)
                ->first()
                ->toArray());

            // proses update data
            Samples::where('sample_uuid', $uuid)
                ->where('sample_log_uuid', null)
                ->update([
                    'sample_status' => 1
                ]);
            DB::commit();

            return $this->output(
                true,
                'Data sample berhasil dihapus',
                $data
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->output(
                false,
                'Data sample gagal dihapus',
                $data
            );
        }
    }

    /* public function update(Request $request)
    {
        $data = array();
        $this->startProcess($request, 'post');

        $uuid = isset($this->param['uuid']) ? $this->param['uuid'] : '';
        $code = isset($this->param['code']) ? $this->param['code'] : '';
        $title = isset($this->param['title']) ? $this->param['title'] : '';
        $description = isset($this->param['description']) ? $this->param['description'] : '';

        DB::beginTransaction();
        try {

            // simpan log data
            Samples::insert(Samples::select(
                'sample_uuid',
                'sample_code',
                'sample_text',
                'sample_date',
                'sample_datetime',
                'sample_number',
                'sample_file_name',
                'sample_file_path',
                'sample_file_size',
                'sample_file_mime',
                'sample_category_uuid',
                'sample_create_by',
                'sample_create_date',
                'sample_status',
                'sample_uuid as sample_log_uuid'
            )
            ->where('sample_log_uuid', null)
            ->where('sample_uuid', $uuid)
            ->first()
            ->toArray());

            // proses update data
            Samples::where('sample_uuid', $uuid)
                ->where('sample_log_uuid', null)
                ->update([
                    'sample_code'           => $code,
                    'sample_title'          => $title,
                    'sample_description'    => $description,
                    'sample_create_date'    => date('Y-m-d H:i:s', time())
                ]);
            DB::commit();
            return $this->output(
                true,
                'Data sample berhasil diperbaharui',
                $data
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->output(
                false,
                'Data sample gagal diperbaharui',
                $data
            );
        }
    } */
}
