<?php
namespace App\Custom;
use App\Mail\VeryfyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class Verifiedmail{
public function __construct() {
  
}

public function SendVerify( $email)

{
    $token=Str::random(5);
   $user=User::where('email',$email)->first();
  
   

   // $url=env('APP_URL').'/api/verified/'.$token;
    
    $data['title']='veryfy email';
    $data['code']=$token;
    $data['email']=$email;
    // Mail::send('verify',['data'=>$data],function($massege) use($data){
    //     $massege->to($data['email'])->subject($data['title']);
    // });

return $token;

}








}







?>