<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Referral extends Model
{
    //

    public static function getCountries(){
    	return DB::table('referrals')->pluck('country')->unique();
    }

    public static function getCities($country){
    	return DB::table('referrals')->where("country", $country)->pluck('city')->unique();
    }
    public function comments(){
        return $this->hasMany('App\Comment');
    }
    public function columns() {
        return Schema::getColumnListing('referrals');
    }
    public function filterData() {
        $places = $filterBase = [];
        $columns = Schema::getColumnListing('referrals');
        $places['names'] = $places['cities'] = [];
        foreach($columns as $column)
        {
            $filterBase[$column] = DB::table('referrals')->pluck($column)->unique();
        }
        $places['names'] = $filterBase['country'];
        foreach($filterBase['country'] as $country)
        {
            $places["cities"][$country] = Referral::where("country", $country)->pluck("city")->unique();
        }
        return ["places" => $places,"filterBase" => $filterBase];
    }
}
