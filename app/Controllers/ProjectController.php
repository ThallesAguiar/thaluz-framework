<?php

namespace App\Controllers;

class ProjectController extends Controller
{
    public function index()
    {
        return $this->json(['message' => 'Hello from ProjectController']);
    }
}
