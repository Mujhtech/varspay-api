<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\PosForm;
use Carbon\Carbon;


class PosFormController extends Controller
{

    public function index()
    {
        $data['title']='POS Form';
        $data['posform']=PosForm::all();
        return view('admin.posform.index', $data);
    }
    
    public function accept($id){
        
        $posform = PosForm::find($id);
        
        $email = $posform->email;
        $name = $posform->name;
        
        $posform->status = 1;
        
        if($posform->save()){
            $message = "Congratulation, your POS AGENT FORM has been apporved by the admin";
            send_email($email, $name, 'POS Agent Form Submit Response', $message);
            
            return back()->with('success', 'POS Form Approved');
        }
        
    } 
    
    public function reject($id){
        
        $posform = PosForm::find($id);
        
        $email = $posform->email;
        $name = $posform->name;
        
        $posform->status = 2;
        
        if($posform->save()){
            $message = "Oops, your POS AGENT FORM has been rejected by the admin. Try again";
            send_email($email, $name, 'POS Agent Form Submit Response', $message);
            
            return back()->with('success', 'POS Form Rejected');
        }
        
    } 
    
}
