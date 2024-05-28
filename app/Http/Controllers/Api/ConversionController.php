<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConversionRequest;
use App\Mail\NotExistincyCurrencyMail;
use App\Models\Conversion;
use App\Services\ConversionServiceInterface;
use App\Services\EloquentConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ConversionController extends Controller
{
    public function __invoke(ConversionRequest $request, ConversionServiceInterface $conversionService)
    {
        $result = $conversionService->convert($request->from, $request->to, $request->amount);

        if ($result) {
            return response()->json(['result' => $result]);
        }

        Mail::to(['address' => 'admin@admin.it'])
            ->send(new NotExistincyCurrencyMail($request->from, $request->to));

        return response()
            ->json(['message' => "Conversion rate for currencies {$request->from} and {$request->to} does not exist"], 404);
    }
}
