@extends('errors.layout')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Sorry! You have attempt Too Many Requests, Please wait a moment and try again'))
