<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

  // public function index(){
  //       $products=array(
  //           [
  //               'name'=>'Product 1',
  //               'price'=>100,
  //               'desc'=>'this is a product',
  //               'qty'=>12,
  //               'image'=>null
  //           ],
  //           [
  //               'name'=>'Product 2',
  //               'price'=>200,
  //               'desc'=>'this is a product',
  //               'qty'=>34,
  //               'image'=>null
  //           ],
  //           );
  //   return response([
  //       'status' => true,
  //       'products' => $products,
  //       "message"=>" thy sodanan",

  //   ],200);
  // }
  // public function store(Request $request){
  //   $product=[];
  //   array_push($product,[
  //               'name'=>$request->name,
  //               'price'=>$request->price,
  //               'desc'=>$request->desc,
  //               'qty'=>$request->qty,
  //            ]);
  //   return response([
  //       'status' => true,
  //       'products' => $product,
  //       "message"=>"product created successfully",
  //   ],201);
  // }
    public function index(){
        // $products=array(
        //     [
        //         'name'=>'Product 1',
        //         'price'=>100,
        //         'desc'=>'this is a product',
        //         'qty'=>12,
        //         'image'=>null
        //     ],
        //     [
        //         'name'=>'Product 2',
        //         'price'=>200,
        //         'desc'=>'this is a product',
        //         'qty'=>34,
        //         'image'=>null
        //     ],
        //     );
        $products=Product::orderBy('id','DESC')->get();
        if($products==null){
            return response([
                'status'=>false,
               'message'=>' products Empty',
            ],204);
        }
        return response([
            'status'=>true,
            'message'=>'selected successfully',
            'product'=>$products,
        ],status:200);
    }
    public function store(Request $request) { 
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'price'=>'required',
            'desc'=>'required',
            'qty'=>'required',
        ]);
        if($validator->fails()){
            return response([
                'status'=>false,
                'errors'=>$validator->errors(),
               'message'=>'validation failed',
            ],422);
        }
        if($validator->passes()){
           $product=new Product();
           $product->name=$request->name;
           $product->price=$request->price;
           $product->desc=$request->desc;
           $product->qty=$request->qty;
           // Add this after the image upload block
if (!$request->hasFile('image')) {
    $product->image = null; // or set a default image URL
}
           if($request->hasFile('image')){
            $file=$request->file('image');
            $fileName=rand(0,999999999).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads'),$fileName);
            // store to db
            $product->image="http://127.0.0.1:8000/uploads/$fileName";
           }
           $product->save();
     }
        // $products=[];
        //push to products
        // array_push($products,[
        //     'name'=>$request->name,
        //     'price'=>$request->price,
        //     'desc'=>$request->desc,
        //     'qty'=>$request->qty,
        //  ]);
        return response([
            'status'=>true,
            'message'=>'product created successfully',
            'product'=>$product,
        ],201);   
    }
    public function update(Request $request,string $id) {
       $validator=Validator::make($request->all(),[
        'name'=>'required',
        'price'=>'required',
        'qty'=>'required',
       ]);
       if($validator->fails()){
        return response([
            'status'=>false,
            'errors'=>$validator->errors(),
            'message'=>'validation failed',
        ],422);
       }
       $product=Product::find($id);
       if($product==null){
        return response([
            'status'=>false,
           'message'=>"Product not found with id",$id
        ],404);
       }
       $product->name=$request->name;
       $product->price=$request->price;
       $product->desc=$request->desc;
       $product->qty=$request->qty;
       if($request->hasFile("image")){
           if($product->image!=null){
               $image=$product->image;
               $imagePath=public_path('uploads/'.basename($image));
               if(File::exists($imagePath)){
                   File::delete($imagePath);
                }
            }
            $file=$request->file('image');
            $fileName=rand(0,999999999).'.'.$file->getClientOriginalExtension();
            //move to folder
            $file->move(public_path('uploads'),$fileName);
            //MOVE TO DB
            $product->image="http://127.0.0.1:8000/uploads/$fileName";  //store image url to db
        }
       $product->save();
       return response([
        'status'=>true,
        'message'=>'Product updated successfully',
         'product'=>$product,
       ],201);
    }
    public function getById(string $id){
      $product=Product::find($id);
      if($product==null){
        return response([
          'status'=>false,
           'message'=>"Product not found with id",$id
        ],404);
      }
      return response([
        'status'=>true,
        'message'=>'Product found successfully',
         'product'=>$product,
      ],200);
    }
    public function destroy(string $id) {
        $product=Product::find($id);
        if($product==null){
            return response([
                'status' =>false,
                'message'=>"Product not found with id",$id
            ],404);
        }
        //delete image from folder
        $image=$product->image;
        //http://127.0.0.1:8000/uploads/23341233.png
        $imageName=basename($image);   //convert string to array and get   value 23341233.png
        //['http:,'','12u7,0,1:8000','images','23341233.png']
        $imagePath=public_path("uploads/$imageName");
        if(File::exists($imagePath)){
           File::delete($imagePath);
        }
        $product->delete();
        return response([
           'status'=>true,
           'message'=>'Product deleted successfully',
        ],200);
    }
}
