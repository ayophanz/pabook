<?php

namespace App\Helpers;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\UserMeta;
use App\Models\RoomMeta;
use App\Models\Option;
use Storage;

class Helpers
{

    /**
    *  Owner Id security verification
    */
    public static function ownerId() {
        return auth('api')->user()->id;
    }

    /**
    *  User Id security verification
    */
    public static function recep() {
      return UserMeta::select('value')->where('user_id', auth('api')->user()->id)->get()->toArray();
    }

    /**
    *  Get rooms belongs to users
    */
    public static function getRoomsIdBaseUser($userType) {
        $owner = 0;
        if($userType=='recep') 
          $owner = UserMeta::select('user_id')->where('meta_key', 'receptionist_id')->where('value', auth('api')->user()->id)->first()->user_id;
        
        if($userType=='owner')
          $owner = auth('api')->user()->id;
        
        $hotel = Hotel::select('id')->where('user_id', $owner)->get()->toArray();
        $types = RoomType::select('id')->whereIn('hotel_id', $hotel)->get()->toArray();
        $room = Room::select('id')->whereIn('id', $types)->get()->toArray();
        return $room;
    }

    /**
    *  validate action by user type
    */
    public static function validateAction($userType, $id) {
        $book = Booking::select('room_id')->where('id', $id)->first()->room_id;
        foreach ($this->getRoomsIdBaseUser($userType) as  $item) {
          if($item['id']==$book)
            return true;
        }
        return false;
      
    }

    /**
    *  Users to notify
    */
    public static function userToNotify($id) {
        $room = Room::select('room_type_id')->where('id', $id)->first();
        $roomType = RoomType::select('hotel_id')->where('id', $room->room_type_id)->first();
        $hotel = Hotel::select('user_id')->where('id', $roomType->hotel_id)->first();
        $owner = User::select('id')->where('id', $hotel->user_id)->first();
        $recep = UserMeta::select('value')->where('meta_key', 'receptionist_id')->where('user_id', $owner->id)->get()->toArray();
        $permission = UserMeta::select('value', 'user_id')->where('meta_key', 'assign_to_hotel')->whereIn('user_id', $recep)->get();
  
        $allowed = array();
        array_push($allowed, $owner->id);
        foreach ($permission as $item) {
            $ids = explode(',', substr($item->value, 1, -1));
            foreach ($ids as $item2) {
              if((int)$roomType->hotel_id==(int)$item2) {
                 array_push($allowed, (int)$item->user_id);
                 continue;
              } 
            }
        }
        return User::whereIn('id', $allowed)->orwhere('role', 'super_admin')->get();
    }

    /**
    *  If does have base currency
    */
    public static function baseCurrencyExist($id, $base_currency) {
        $option = [
                    'meta_key' => 'base_currency',
                    'meta_value' => $id,
                    'value' => $base_currency
                  ];
        $exist = Option::where('meta_key', 'base_currency')->where('meta_value', $id)->first();
        if($exist) {
            if($base_currency=='use_global')
                Option::where('meta_key', 'base_currency')->where('meta_value', $id)->delete();
            else
                Option::where('meta_key', 'base_currency')->where('meta_value', $id)->update($option);
        }else{
             if($base_currency!='use_global')
                Option::create($option);
        }
    }

    /**
    *  File upload
    */
    public static function uploadFile($base64_file, $path, $name) {
        $file = $base64_file;
        $check_base64 = strrpos($file, "base64");
        if($check_base64 > 0) {
            $explode = explode(",", $file);
            $decode_file = base64_decode($explode[1]);
            $file_extension = self::uf_get_base64_file_extension($explode[0]);
            $filename = $name.'.'.$file_extension ;
            Storage::disk('public')->put($path."/".$filename, $decode_file, "public");
            $url = Storage::url($path."/".$filename);
            return $url;
        }else{
            return $file;
        }
    }

    public static function uf_get_base64_file_extension($base64_raw_file) {
        $mime = str_replace(";base64", '', $base64_raw_file);
        $mime = str_replace("data:", '', $mime);
        $extension_arr = [
            "application/zip" => "zip",
            "application/x-zip-compressed" => "zip"
        ];
        return $extension_arr[$mime];
    }

    /**
    *  Create and update Option
    */
    public static function createUpdateOption($id, $dataCreate) {
        $option = null;
        $exist = Option::where('meta_key', 'base_currency')->where('meta_value', $id)->first();
        if(!$exist) {
          $option = Option::create($dataCreate);
        }else{
          $option = Option::where('meta_key', 'base_currency')->where('meta_value', $id)->update($dataCreate);
        }

        return $option;
    }

    /**
    *  update amenities
    */
    public static function updateAmenities($type, $featureData, $room_id) {
      $featureDataTemp = array_filter($featureData, function($v) { return !is_null($v['value']); });
      if(json_encode($featureDataTemp)!='[[]]') {
          $dataMetaUpdate = ['value' => json_encode($featureDataTemp)];
          if(RoomMeta::where('room_id', $room_id)->where('meta_key', $type)->get()->count()) {
            RoomMeta::where('room_id', $room_id)->where('meta_key', $type)->update($dataMetaUpdate);
          }else{
            $dataMetaCreate = [
              'room_id'  => $room_id,
              'meta_key' => $type,
              'value' => json_encode($featureDataTemp)
            ];
            RoomMeta::create($dataMetaCreate);
          }
        }
    }

    /**
    *  create amenities
    */
    public static function amenities($type, $featureData, $room_id) {
      $featureDataTemp = array_filter($featureData, function($v) { return !is_null($v['value']); });
      if(json_encode($featureDataTemp)!='[[]]') {
        $dataMetaCreate = [
                          'room_id'  => $room_id,
                          'meta_key' => $type,
                          'value'    => json_encode($featureDataTemp)
                          ];               
        RoomMeta::create($dataMetaCreate);
      }
    }

    /**
    *  create rooms no.
    */
    public static function roomsNo($roomsNoData, $room_id, $hotel_id) {
        $hotel = Hotel::where('id', $hotel_id)->first();
        $newData = json_decode($hotel->hotel_rooms_no, true); 
        foreach($newData as &$val) {
          if($val['assign_id']==$room_id && $val['status']=='ready') $val['assign_id'] = 'no';
          foreach($roomsNoData as $val2) {
            if($val['code']==$val2['code']) {
              $val['status'] = $val2['status'];
              $val['assign_id'] = $room_id;
              continue;
            }
          }
        }
        $data = ['hotel_rooms_no' => json_encode($newData)];
        Hotel::where('id', $hotel_id)->update($data);
    }

    /**
    *  Hotel owner
    */
    public static function hotelOwner($roomType=0) {
        $user = auth('api')->user();
        if($roomType==0) {
          $hotel = Hotel::select('id')->where('status', 'verified')->where('user_id', $user->id)->pluck('id')->toArray();
          if(empty($hotel)) {
            $userMeta = UserMeta::select('user_id')->where('value', $user->id)->first();
            if(!empty($userMeta))
              $hotel = Hotel::select('id')->where('status', 'verified')->where('user_id', $userMeta->user_id)->pluck('id')->toArray();
          }

          $roomType = RoomType::select('id')->whereIn('hotel_id', $hotel)->pluck('id')->toArray();
          foreach ($roomType as $key => $value) { unset($roomType[$key]['room_type_hotel']); }

          return $roomType;
        }
    }

    /**
    *  Get owner hotels ID
    */
    public static function hotel_ids() {
        return Hotel::where('user_id', auth('api')->user()->id)->pluck('id')->toArray();
    }

    /**
    * Gallery upload
    */
    public static function imageGallery($images, $id, $action) {
        $file = [];                     
        foreach ($images as $key => $subArr) {
            $image = $subArr['1']['filename'];
            $fileExist = public_path().'/storage/images/upload/roomImages/gallery-'.$id.'/'.$image;
            if($action=='update' && File::exists($fileExist))
              unlink(public_path('storage/images/upload/roomImages/gallery-'.$id.'/'.$image));
            \Image::make($subArr['2']['image'])->save(storage_path('app/public/images/upload/roomImages/gallery-'.$id.'/').$image); 
            unset($subArr['2']);
            $file[$key] = $subArr;  
        }
        return $file;
    }

    /**
    * Feature image upload
    */
    public static function featureImage($img, $id, $name, $action) {
        $folder = public_path().'/storage/images/upload/roomImages/gallery-'.$id.'/';
        if (!File::exists($folder)) 
            File::makeDirectory($folder, 0775, true);

        $image =  $name.'-'.$id.'.'.explode('/', 
            explode(':', substr($img, 0, 
            strpos($img, ';')))[1])[1];
        if($action=='update') {
          $fileExist = public_path().'/storage/images/upload/roomImages/gallery-'.$id.'/'.$name.'-'.$id;
          if (File::exists($fileExist.'.png')) unlink($fileExist.'.png');
          elseif (File::exists($fileExist.'.jpg')) unlink($fileExist.'.jpg');
          elseif (File::exists($fileExist.'.jpeg')) unlink($fileExist.'.jpeg');
          elseif (File::exists($fileExist.'.gif')) unlink($fileExist.'.gif');
        }  
        \Image::make($img)->save(public_path('storage/images/upload/roomImages/gallery-'.$id.'/').$image);
        return $image;
    }

    /**
    *  Get unassigned room types
    */
    public static function roomTypeIds($hotelId, $roomId) {
      $roomType = RoomType::select('id')->where('hotel_id', $hotelId)->get()->toArray();
      foreach ($roomType as $key => $value) {
        unset($roomType[$key]['room_type_hotel']);
      }
      
      $rooms = Room::select('room_type_id')->whereIn('room_type_id', $roomType)->get()->toArray();
      if($roomId!=null) 
        $rooms = Room::select('room_type_id')->where('id', '!=', $roomId)->whereIn('room_type_id', $roomType)->get()->toArray();

      foreach ($rooms as $key => $value) {
        unset($rooms[$key]['room_type']);
        unset($rooms[$key]['room_feature']);
        unset($rooms[$key]['room_gallery']);
      }

      return $rooms;
    }
    
}