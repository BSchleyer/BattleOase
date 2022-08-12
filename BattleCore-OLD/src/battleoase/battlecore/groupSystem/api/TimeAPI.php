<?php

namespace battleoase\battlecore\groupSystem\api;


class TimeAPI
{

    public static function convert(int $time): TimeFormat{
        $c = $time - time();
        if($c >= 31104000){
            $years = floor($c/31104000);
            $c = $c - $years*31104000;
        } else {
            $years = 0;
        }
        if($c >= 2592000){
            $months = floor($c/2592000);
            $c = $c - $months*2592000;
        } else {
            $months = 0;
        }
        if($c >= 86400){
            $days = floor($c/86400);
            $c = $c - $days*86400;
        } else {
            $days = 0;
        }
        if($c >= 3600){
            $hours = floor($c/3600);
            $c = $c - $hours*3600;
        } else {
            $hours = 0;
        }
        if($c >= 60){
            $minutes = floor($c/60);
            $c = $c - $minutes*60;
        } else {
            $minutes = 0;
        }
        if($c >= 1) {
            $seconds =  floor($c/1);
        } else {
            $seconds = 0;
        }
        return new TimeFormat($years, $months, $days, $hours, $minutes, $seconds);
    }

    public static function getTimeFromArray(array $times) : TimeFormat
    {
        $years = 0; $months = 0; $days = 0; $hours = 0; $minutes = 0; $seconds = 0;
        foreach ($times as $time) {
            if ($time != ($result = str_replace("Y", "", $time))) {
                $years = $result;
            } elseif ($time != ($result = str_replace("M", "", $time))) {
                $months = $result;
            } elseif ($time != ($result = str_replace("d", "", $time))) {
                $days = $result;
            } elseif ($time != ($result = str_replace("h", "", $time))) {
                $hours = $result;
            } elseif ($time != ($result = str_replace("m", "", $time))) {
                $minutes = $result;
            } elseif ($time != ($result = str_replace("s", "", $time))) {
                $seconds = $result;
            }
        }
        return new TimeFormat(intval($years), intval($months), intval($days), intval($hours), intval($minutes), intval($seconds));
    }

    public static function isTimeValid(int $time)
    {
        if ($time != 0) {
            $c = $time - time();
            if ($c <= 0) {
                return false;
            }
        }

        return true;
    }

}