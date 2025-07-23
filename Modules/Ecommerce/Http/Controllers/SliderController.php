<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;

use Modules\Ecommerce\Entities\Sliders;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Session;
use DB;
use Cache;

class SliderController extends Controller
{
    use \App\Traits\CacheForget;
    use \App\Traits\TenantInfo;

    public function slidersShow()
    {
        $sliders = DB::table('sliders')->orderBy('order', 'asc')->get();

        return view ('ecommerce::backend.slider.sliders', compact('sliders'));
    }

    public function slidersCreate(Request $request)
    {
        if(!env('USER_VERIFIED')){
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }
       
        $titles = $request->input('title');
        $links = $request->input('link');
        $orders = $request->input('order');
        
        foreach($orders as $key=>$order) {  
            
            $data = [
                'order'  => $orders[$key],
                'title'  => $titles[$key],
                'link'   => $links[$key],
            ];

            $sliders = Sliders::create($data);

            $id = $sliders->id;  

            $image_name = date('Ymdhis');

            if(isset($request->image1[$key])) { 
                $image1 = $request->image1[$key];
                if ($image1) {
                    $ext = pathinfo($image1->getClientOriginalName(), PATHINFO_EXTENSION);
                    $imageName = $image_name . $key;

                    if(!config('database.connections.saleprosaas_landlord')) {
                        $imageName = $imageName . '.' . $ext;
                    }
                    else {
                        $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;        
                    }     

                    $image1->move(public_path('frontend/images/slider/desktop'), $imageName);

                    // $manager = new ImageManager(Driver::class);
                   $manager = new ImageManager(['driver' => 'gd']); // Initialize the ImageManager with the GD driver

// Ensure the image path is correct
$imagePath = public_path('frontend/images/slider/desktop/') . $imageName;

// Create an image instance
$image = $manager->make($imagePath);

// Resize and crop the image to fit 1900x970 dimensions
$image->save($imagePath, 100); // Save with 100% quality

// Assign the image name to your data array
$data['image1'] = $imageName;
                }

                $sliders->image1 = $data['image1'];
                $sliders->save();
            }

            if(isset($request->image2[$key])) { 
                $image2 = $request->image2[$key];
                if ($image2) {
                    $ext = pathinfo($image2->getClientOriginalName(), PATHINFO_EXTENSION);
                    $imageName = $image_name . $key;
                    
                    if(!config('database.connections.saleprosaas_landlord')) {
                        $imageName = $imageName . '.' . $ext;
                    }
                    else {
                        $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;
                    }  

                    $image2->move(public_path('frontend/images/slider/tab'), $imageName);

$manager = new ImageManager(['driver' => 'gd']); // Initialize with GD driver

// Define the image path
$imagePath = public_path('frontend/images/slider/tab/') . $imageName;

// Ensure the file exists before processing
if (!file_exists($imagePath)) {
    throw new \Exception("Image not found at: {$imagePath}");
}

// Load and process the image
$image = $manager->make($imagePath)
    ->save($imagePath, 100); // Save the processed image with 100% quality

// Assign the image name to the data array
$data['image2'] = $imageName;
                }

                $sliders->image2 = $data['image2'];
                $sliders->save();
            }

            if(isset($request->image3[$key])) { 
                $image3 = $request->image3[$key];
                if ($image3) {
                    $ext = pathinfo($image3->getClientOriginalName(), PATHINFO_EXTENSION);
                    $imageName = $image_name . $key;
                    
                    
                    if(!config('database.connections.saleprosaas_landlord')) {
                        $imageName = $imageName . '.' . $ext;
                    }
                    else {
                        $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;
                    } 

                    $image3->move(public_path('frontend/images/slider/mobile'), $imageName);

                    // $manager = new ImageManager(Driver::class);
                   $manager = new ImageManager(['driver' => 'gd']); // Initialize the ImageManager with the GD driver

// Define the image path
$imagePath = public_path('frontend/images/slider/mobile/') . $imageName;

// Check if the image exists
if (!file_exists($imagePath)) {
    throw new \Exception("Image not found: {$imagePath}");
}

// Load and resize the image
$image = $manager->make($imagePath)
    ->fit(650, 900) // Resize and crop the image to fit 650x900 dimensions
    ->save($imagePath, 100); // Save the image with 100% quality

// Assign the image name to the data array
$data['image3'] = $imageName;
                }

                $sliders->image3 = $data['image3'];
                $sliders->save();
            }

        }

        cache()->forget('sliders');

        Session::flash('message', 'Sliders inserted successfully.');
        Session::flash('type', 'success');

        return redirect()->back();
    }

    public function slidersDelete($id)
    {
        if(!env('USER_VERIFIED')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }

        $slide = Sliders::find($id);
        $slide->delete();

        $this->fileDelete(public_path('frontend/images/slider/desktop/'), $slide->image1);
        $this->fileDelete(public_path('frontend/images/slider/tab/'), $slide->image2);
        $this->fileDelete(public_path('frontend/images/slider/mobile/'), $slide->image3);

        cache()->forget('sliders');

        Session::flash('message', 'Slider deleted successfully.');
        Session::flash('type', 'success');

        return redirect()->back();
    }

}
