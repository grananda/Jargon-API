<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Communication\DeleteMemoRequest;
use App\Http\Requests\Communication\IndexMemoRequest;
use App\Http\Requests\Communication\ShowMemoRequest;
use App\Http\Requests\Communication\StoreMemoRequest;
use App\Http\Requests\Communication\UpdateMemoRequest;

class MemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Communication\IndexMemoRequest $request
     *
     * @return void
     */
    public function index(IndexMemoRequest $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Communication\StoreMemoRequest $request
     *
     * @return void
     */
    public function store(StoreMemoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Communication\ShowMemoRequest $request
     *
     * @return void
     */
    public function show(ShowMemoRequest $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Communication\UpdateMemoRequest $request
     *
     * @return void
     */
    public function update(UpdateMemoRequest $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Communication\DeleteMemoRequest $request
     *
     * @return void
     */
    public function destroy(DeleteMemoRequest $request)
    {
        //
    }
}
