@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('picture', URL::asset('/images/404.png'))
@section('message', __('Nenalezeno'))
