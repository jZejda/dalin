@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('picture', URL::asset('/images/500.png'))
@section('message', __('Asi se něco nepovedlo'))
