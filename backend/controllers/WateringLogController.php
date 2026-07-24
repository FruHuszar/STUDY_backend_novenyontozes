<?php

declare(strict_types=1);

final class WateringLogController
{
    private const SOURCES = ['manual', 'auto'];

    public function __construct(private readonly WateringService $waterings)
    {
    }

    public function index(Request $request): Response
    {
        return Response::json(200, $this->waterings->history($request->id()));
    }

    public function store(Request $request): Response
    {
        $validator = $request->validator();

        $amountMl = $validator->optionalNonNegativeInt('amountMl');
        $source = $validator->enum('source', self::SOURCES, 'manual');

        $validator->assertValid();

        $log = $this->waterings->water($request->id(), $amountMl, $source);

        return Response::created('/api/waterings/' . $log->getId(), $log);
    }

    public function statistics(Request $request): Response
    {
        return Response::json(200, $this->waterings->statistics());
    }
}
