<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assistance;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\Pdf\ReportPdf;
use Interpid\PdfLib\Multicell;
use Interpid\PdfLib\Table;

class AssistanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* 
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable',
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'academy_id' => ['nullable', 'integer', 'exists:academies,id'],
            'date_from' => ['nullable', 'date_format:"d-m-Y"'],
            'date_to' => ['nullable', 'date_format:"d-m-Y"'],
            'exams' => 'nullable',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.assistances.index')
                            ->withErrors($validator)->withInput();
        }
        
        $assistances = $this->data($request)->paginate(10);
                
        return view('admin.assistances.index', compact('assistances'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.assistances.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        $request->merge($search);
                
        $model = $this->data($request);
        
        $total = $model->count();

        /*
        if (is_array($search) && !empty($search['value'])) {
            $model->where(function($query) use ($search) {
                $words = explode(' ', $search['value']);
                
                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',s.name,' ',s.last_name) like '% ". $word ."%'");
                }
            });
        }
        */
        
        $report = [
            'search' => $search,
            'order' => $order,
            'columns' => $columns,
        ];        

        $filtered = $model->count();
        
        if (is_array($order)) {
            foreach ($order as $value) {
                $column = $value['column'];
                $dir = $value['dir'];
                $model->orderBy($columns[$column]['name'], $dir);
            }
        }
        
        $results = $model->offset($start)->limit($length)->get();
        
        $dt = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $results,
            'report' => $report,
        ];
        
        return response()->json($dt);        
    }     
    
    public function report($report = null, Request $request)
    {
        $pdf = new ReportPdf('P');
        
        $data = [];

        if ($request->has('_')) {
            $data = json_decode(base64_decode($request->_), true);
        }

        extract($data);        
        
        $request->merge($search);
        
        $pdf->title = mb_strtoupper('Reporte de asistencia');

        $description = [];
        
        if ($request->filled('date_from')) {
            $description[] = 'desde el '. $request->date_from;
        }
        
        if ($request->filled('date_to')) {
            $description[] = "hasta el ". $request->date_to;
        }        

        if ($request->user()->hasAnyRole(['instructor', 'country-manager','latam-manager','admin'])) {
            if ($request->filled('country_id')) {
                $country = \App\Models\Country::find($request->country_id);
                $description[] = $country->name;
            }
        }

        $pdf->description = mb_strtoupper(join(' - ', $description));

        $multicell = new Multicell($this);

        $pdf->SetAuthor('UPLOAD');
        $pdf->SetTitle($pdf->title);
        $pdf->SetSubject('');
        $pdf->SetKeywords('');
        
        $pdf->SetMargins(20, 22, 20);
        $pdf->SetAutoPageBreak(TRUE, 10);

        $pdf->AddFont('arial', '', 'arial.ttf', true);
        $pdf->AddFont('arial', 'B', 'arialbd.ttf', true);
        $pdf->AddFont('arial', 'BI', 'arialbi.ttf', true);
        $pdf->AddFont('arial', 'I', 'ariali.ttf', true);    

        $pdf->AddPage();
        $pdf->AliasNbPages();
        
        $table = new Table($pdf);

        $config = [
            'TABLE' => [
                'TABLE_ALIGN' => 'L',
                'TABLE_LEFT_MARGIN' => 0,
                'BORDER_COLOR' => [0, 0, 0],
                'BORDER_SIZE' => '0',
            ],

            'HEADER' => [
                'TEXT_COLOR' => [255, 255, 255],
                'TEXT_SIZE' => 10,
                'TEXT_FONT' => 'arial',
                'LINE_SIZE' => 6,
                'BORDER_SIZE' => 0,
                'BORDER_TYPE' => '1',
                'BORDER_COLOR' => [0, 0, 0],
            ],

            'ROW' => [
                'TEXT_COLOR' => [0, 0, 0],
                'TEXT_SIZE' => 10,
                'TEXT_FONT' => 'arial',
                'BORDER_SIZE' => 0,
                'BORDER_TYPE' => '1',
                'BACKGROUND_COLOR' => [255, 255, 255],
                'BORDER_COLOR' => [0, 0, 0],
                'PADDING_TOP' => 1,
            ],
        ];

        $table->initialize([10, 25, 25, 55, 55], $config);

        $header = [
            ['TEXT' => ' '],
            ['TEXT' => 'Fecha'],
            ['TEXT' => 'Hora'],
            ['TEXT' => 'Academia'],
            ['TEXT' => 'Alumno'],
        ];

        $table->addHeader($header);

        $model = $this->data($request);
        
        if (is_array($order)) {
            foreach ($order as $value) {
                $column = $value['column'];
                $dir = $value['dir'];
                $model->orderBy($columns[$column]['name'], $dir);
            }
        }        
        
        $i = 0;
        
        foreach ($model->get() as $item) {
            $i++;
            
            $row = [
                ['TEXT' => $i],
                ['TEXT' => $item->created_at->format('d-m-Y')],
                ['TEXT' => $item->created_at->format('H:i')],
                ['TEXT' => $item->academy->name],
                ['TEXT' => $item->studentUser ? $item->studentUser->name . ' ' . $item->studentUser->last_name : ' '],
            ];

            $table->addRow($row);
        }     

        $table->close();
    
        $pdf->Output();
    }
    
    
    protected function data(Request $request)
    {
        $model = Assistance::select(['assistances.*']);
        
        $model->addSelect([\DB::raw("concat(s.name,' ',s.last_name) as student"), 'a.name as _academy']);
        $model->leftJoin('users as s', 's.id', 'assistances.student_user_id')
            ->join('academies as a', 'a.id', 'assistances.academy_id');        
        
        if ($request->user()->hasAnyRole(['instructor', 'country-manager','latam-manager','admin'])) {
            /*
            if ($request->filled('search')) {            
                $model->whereHas('studentUser', function($query) {
                    $words = explode(' ', request('search'));
                    $query->where(\DB::raw("concat(' ',`name`,' ',`last_name`)"), 'like', '% '. join('% ', $words) .'%');
                });
            }
            */
            
            if ($request->filled('value')) {
                $model->where(function($query) {
                    $words = explode(' ', request('value'));
                    
                    foreach ($words as $word) {
                        $query->orWhereRaw("concat(' ',s.name,' ',s.last_name) like '% ". $word ."%'");
                    }
                });
            }            
            
            if (!$request->user()->hasAnyRole(['latam-manager','admin'])) {
                $request->merge([
                    'country_id' => $request->user()->city->country_id,
                ]);
                
                if (!$request->user()->hasRole('country-manager')) {
                    $request->merge([
                        'academy_id' => $request->user()->academy_id,
                    ]);
                }
            }
            
            if ($request->filled('country_id')) {
                $model->whereHas('academy', function($query) {
                    $query->where('country_id', request('country_id'));
                });
                
                if ($request->filled('academy_id')) {
                    $model->where('assistances.academy_id', $request->academy_id);
                }            
            }
        } else {
            $model->where('assistances.student_user_id', $request->user()->id);
        }            
        
        if ($request->filled('date_from')) {
            $model->whereDate('assistances.created_at', '>=', Carbon::createFromFormat('d-m-Y', $request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $model->whereDate('assistances.created_at', '<=', Carbon::createFromFormat('d-m-Y', $request->date_to));
        }        

        if ($request->boolean('exams')) {
            $model->whereIsExam(true);
        }
        
        //return $model->orderBy('created_at', 'asc');
        return $model;
    }
}
