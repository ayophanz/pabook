<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('image64', function ($attribute, $value, $parameters, $validator) {
            $type = explode('/', explode(':', substr($value, 0, strpos($value, ';')))[1])[1];
            if (in_array($type, $parameters)) {
                return true;
            }
            return false;
        });

        Validator::replacer('image64', function($message, $attribute, $rule, $parameters) {
            return str_replace(':values',join(",",$parameters),$message);
        });

        Validator::extend('unique_name', function ($attribute, $value, $parameters) {
            list($table, $field, $field2, $field2Value, $id) = $parameters;
            if($id=='0')
                return DB::table($table)->where($field, $value)->where($field2, $field2Value)->count() == 0;
            else{
                $currentVal = DB::table($table)->where('id', $id)->first();
                if($currentVal->name==$value) return 1;
                return DB::table($table)->where($field, $value)->where($field2, $field2Value)->count() == 0; 
            }
        });

        Validator::extend('greater_than_field', function($attribute, $value, $parameters, $validator) {
          $min_field = $parameters[0];
          $data = $validator->getData();
          $min_value = $data[$min_field];
          return $value >= $min_value;
        });   

        Validator::replacer('greater_than_field', function($message, $attribute, $rule, $parameters) {
          return str_replace(':field', $parameters[0], $message);
        });

        Validator::extend('match_old_password', function ($attribute, $value, $parameters) {
            list($field_password, $current_password) = $parameters;
            return Hash::check($field_password, $current_password) ? true : false;
        });

        Validator::extend('rooms_no_equal_room_total', function ($attribute, $value, $parameters) {
            list($field_rooms_no, $room_total) = $parameters;
            return ((int)$room_total == (int)$field_rooms_no) ? true : false;
        });

    }
}
