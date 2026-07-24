<?php

declare(strict_types=1);

final class SpeciesController
{
    public function __construct(private readonly SpeciesService $species)
    {
    }

    public function index(Request $request): Response
    {
        return Response::json(200, SpeciesResource::collection($this->species->list($request->queryInt('bloomingIn'))));
    }

    public function show(Request $request): Response
    {
        return Response::json(200, SpeciesResource::make($this->species->find($request->id())));
    }

    public function families(Request $request): Response
    {
        return Response::json(200, $this->species->families());
    }

    public function phases(Request $request): Response
    {
        return Response::json(200, $this->species->phases());
    }
}
